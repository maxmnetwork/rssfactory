<?php

namespace Joomla\Component\Rssfactory\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\Archive\Zip;
use Exception;

/**
 * Backup model for RSS Factory
 *
 * @since  4.0.0
 */
class BackupModel extends AdminModel
{
    protected array $tables = [
        '#__categories',
        '#__rssfactory',
        '#__rssfactory_ads',
        '#__rssfactory_ad_category_map',
    ];

    public function restoreBackup(array $data = []): bool
    {
        $dbo = $this->getDbo();

        // Validate uploaded file
        if (!isset($data['error']) || $data['error'] === 4) {
            $this->setState('error', FactoryTextRss::_('backup_task_restore_error_no_file_uploaded'));
            return false;
        }

        if ($data['error'] !== 0) {
            $this->setState('error', FactoryTextRss::sprintf('backup_task_restore_error_upload_error', $data['error']));
            return false;
        }

        if (strtolower(File::getExt($data['name'])) !== 'zip') {
            $this->setState('error', FactoryTextRss::_('backup_task_restore_error_not_valid_archive'));
            return false;
        }

        // Extract archive
        $zip = new Zip();
        if (!$zip->extract($data['tmp_name'], RSS_FACTORY_TMP_PATH)) {
            $this->setState('error', FactoryTextRss::_('backup_task_restore_error_extracting_archive'));
            return false;
        }

        // Truncate and clear relevant tables
        foreach ($this->tables as $table) {
            if ($table === '#__categories') {
                Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_categories/tables');
                $categoryTable = Table::getInstance('Category', 'Joomla\\CMS\\Table\\');

                $query = $dbo->getQuery(true)
                    ->select('id')
                    ->from($dbo->quoteName('#__categories'))
                    ->where('extension = ' . $dbo->quote('com_rssfactory'));

                foreach ($dbo->setQuery($query)->loadObjectList() as $category) {
                    $categoryTable->load($category->id);
                    $categoryTable->delete($category->id);
                }
                continue;
            }

            $dbo->setQuery('TRUNCATE TABLE ' . $dbo->quoteName($table))->execute();
        }

        $sqlFile = RSS_FACTORY_TMP_PATH . '/RSSFactoryPRO.sql';
        if (!File::exists($sqlFile)) {
            $this->setState('error', Text::_('COM_RSSFACTORY_BACKUP_SQL_FILE_NOT_FOUND'));
            return false;
        }

        $sql = file($sqlFile, FILE_IGNORE_NEW_LINES);
        if (!$this->executeSQL($sql)) {
            return false;
        }

        $this->restoreConfiguration();
        $this->removeTmpBackupFiles();

        return true;
    }

    public function generateBackup(): void
    {
        $zip = new Zip();
        $backupFile = RSS_FACTORY_TMP_PATH . '/RSSFactoryPro_Backup_' . date('Y-m-d') . '.zip';

        $files = [
            [
                'name' => 'configuration.json',
                'data' => ComponentHelper::getParams('com_rssfactory')->toString(),
            ],
            [
                'name' => 'RSSFactoryPRO.sql',
                'data' => $this->getBackupSQL(),
            ],
        ];

        if (!$zip->create($backupFile, $files)) {
            $this->setState('error', FactoryTextRss::_('backup_task_generate_error_create_archive'));
            return;
        }

        $backupName = basename($backupFile);

        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=\"$backupName\"");
        header("Content-Length: " . filesize($backupFile));
        readfile($backupFile);
        File::delete($backupFile);

        exit();
    }

    public function import(array $data, string $separator): bool
    {
        $dbo = $this->getDbo();

        if (!isset($data['error']) || $data['error'] === 4) {
            $this->setState('error', FactoryTextRss::_('backup_task_restore_error_no_file_uploaded'));
            return false;
        }

        if ($data['error'] !== 0) {
            $this->setState('error', FactoryTextRss::sprintf('backup_task_restore_error_upload_error', $data['error']));
            return false;
        }

        $separator = ($separator === 'TAB') ? "\t" : $separator;
        $handle = fopen($data['tmp_name'], 'r');
        $count = 0;

        while (($row = fgetcsv($handle, 10000, $separator)) !== false) {
            if (count($row) !== 3) {
                continue;
            }

            [$cat, $title, $url] = $row;
            $feedTable = Table::getInstance('Feed', 'RssFactoryTable');
            $feedTable->title = $title;

            // Resolve or create category
            if (!ctype_digit($cat)) {
                $query = $dbo->getQuery(true)
                    ->select('id')
                    ->from($dbo->quoteName('#__categories'))
                    ->where('extension = ' . $dbo->quote('com_rssfactory'))
                    ->where('title = ' . $dbo->quote($cat));

                $catId = (int) $dbo->setQuery($query)->loadResult();

                if (!$catId) {
                    $catData = [
                        'parent_id' => 1,
                        'level' => 1,
                        'path' => \Joomla\CMS\Filter\OutputFilter::stringURLSafe($cat),
                        'extension' => 'com_rssfactory',
                        'title' => $cat,
                        'published' => 0,
                        'access' => 1,
                        'language' => '*',
                    ];

                    $category = $this->insertCategory($catData);
                    $feedTable->cat = $category ? $category->id : 0;
                } else {
                    $feedTable->cat = $catId;
                }
            } else {
                $feedTable->cat = (int) $cat;
            }

            $feedTable->url = $url;
            $feedTable->published = 0;
            $feedTable->ordering = $count;

            if ($feedTable->store()) {
                $count++;
            }
        }

        fclose($handle);
        return true;
    }

    public function executeSQL(array $sql): bool
    {
        Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_categories/tables');
        Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rssfactory/tables');

        $dbo = $this->getDbo();
        $ids = [];
        $restored = ['categories' => 0, 'feeds' => 0, 'ads' => 0];
        $errors = [];

        foreach ($sql as $line) {
            $decoded = json_decode($line);
            if (!$decoded) {
                continue;
            }

            $data = (array) $decoded->definition;

            switch ($decoded->table) {
                case '#__rssfactory':
                    $table = Table::getInstance('Feed', 'RssFactoryTable');
                    break;

                case '#__rssfactory_ads':
                    $table = Table::getInstance('Ad', 'RssFactoryTable');
                    $categories = new Registry($data['categories_assigned']);
                    $data['categories_assigned'] = (new Registry(array_map(fn($id) => $ids[$id] ?? null, $categories->toArray())))->toString();
                    break;

                case '#__categories':
                    $table = Table::getInstance('Category', 'Joomla\\CMS\\Table\\');
                    $oldId = $data['id'];
                    unset($data['id'], $data['asset_id'], $data['lft'], $data['rgt'], $data['path']);

                    if (isset($data['parent_id']) && $data['parent_id'] !== 1) {
                        $data['parent_id'] = $ids[$data['parent_id']] ?? 1;
                    }

                    $table->setLocation($data['parent_id'], 'last-child');
                    if ($table->save($data)) {
                        $ids[$oldId] = $table->id;
                        $restored['categories']++;
                    } else {
                        $errors[] = $data;
                    }
                    continue 2;

                case '#__rssfactory_ad_category_map':
                    $table = Table::getInstance('AdCategoryMap', 'RssFactoryTable');
                    $data['categoryId'] = $ids[$data['categoryId']] ?? $data['categoryId'];
                    break;

                default:
                    continue 2;
            }

            $table->bind($data);
            if (!$dbo->insertObject($decoded->table, $table)) {
                $errors[] = $data;
            } else {
                $restored[str_replace('#__rssfactory_', '', $decoded->table)]++;
            }
        }

        $table = Table::getInstance('Category', 'Joomla\\CMS\\Table\\');
        $table->rebuild();

        if ($errors) {
            $this->setState('error', implode('<br>', array_map('json_encode', $errors)));
        }

        $this->setState('restored', $restored);
        return true;
    }

    protected function generateTableBackup(string $table): string
    {
        $dbo = $this->getDbo();
        $query = $dbo->getQuery(true)->select('*')->from($dbo->quoteName($table));

        if ($table === '#__categories') {
            $query->where('extension = ' . $dbo->quote('com_rssfactory'));
        }

        $rows = $dbo->setQuery($query)->loadObjectList();

        return implode(PHP_EOL, array_map(fn($row) => json_encode(['table' => $table, 'definition' => $row]), $rows));
    }

    protected function getBackupSQL(): string
    {
        $sql = array_map(fn($t) => $this->generateTableBackup($t), $this->tables);

        $aboutModel = new \Joomla\Component\Rssfactory\Administrator\Model\AboutModel();
        $sql[] = PHP_EOL . '/*version=' . $aboutModel->getCurrentVersion() . '*/';

        return implode(PHP_EOL, $sql);
    }

    protected function removeTmpBackupFiles(): void
    {
        if (!is_dir(RSS_FACTORY_TMP_PATH)) {
            return;
        }

        foreach (array_diff(scandir(RSS_FACTORY_TMP_PATH), ['.', '..']) as $file) {
            if (!str_ends_with($file, '.htaccess')) {
                $fullPath = RSS_FACTORY_TMP_PATH . DIRECTORY_SEPARATOR . $file;
                is_file($fullPath) ? File::delete($fullPath) : Folder::delete($fullPath);
            }
        }
    }

    protected function restoreConfiguration(): bool
    {
        $configFile = RSS_FACTORY_TMP_PATH . '/configuration.json';

        if (!File::exists($configFile)) {
            return false;
        }

        $json = file_get_contents($configFile);
        $params = new Registry($json);

        $extension = Table::getInstance('Extension');
        $id = $extension->find(['type' => 'component', 'element' => 'com_rssfactory']);
        $extension->load($id);
        $extension->params = $params->toString();

        return $extension->store();
    }

    private function insertCategory(array $data)
    {
        try {
            $table = Table::getInstance('Category');
            $table->setLocation(1, 'last-child');
            $table->save($data);
            return $table;
        } catch (Exception $e) {
            return false;
        }
    }
}
