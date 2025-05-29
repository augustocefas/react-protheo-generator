<?php

namespace App\Generator;
use App\Generator\Generators\{
    ControllerGenerator, 
    ModelGenerator,
    FormGenerator,
    TableGenerator,
    EntryPageGenerator
};

class CrudGenerator 
{
    private string $moduleName;
    public string $basePath;
    public array $fields;

    public $updateKey;
    public $deleteKey;
    public $masterKey;

    public function __construct(string $moduleName, string $basePath = 'output')
    {
        $this->moduleName = $moduleName;
        $this->basePath = rtrim($basePath, '/');
    }

    public function setMasterKey($masterKey): void
    {
        if (empty($masterKey)) {
            throw new \InvalidArgumentException("A chave mestre não pode ser vazia.");
        }
        $this->masterKey = $masterKey;
    }

    public function setFields($fields){
        if(!is_array($fields) || empty($fields)) {
            throw new \InvalidArgumentException("Os campos devem ser um array não vazio.");
        }
        $this->fields = $fields;
    }

    public function setDeleteUpdateKey($a, $b, $type): void
    {
        if($type=='update') $this->updateKey = [$a, $b];
        if($type=='delete') $this->deleteKey = [$a, $b];
    }

    public function createModuleFolders(): void
    {
        
        if (!is_dir($this->basePath)) {
            if (!mkdir($this->basePath, 0777, true)) {
                echo "❌ Não foi possível criar a pasta base: {$this->basePath}\n";
                return;
            }
        }
    
        // Verifica permissão
        if (!is_writable($this->basePath)) {
            echo "❌ Sem permissão de escrita na pasta base: {$this->basePath}\n";
            chmod($this->basePath, 0777);
            return;
        }

        
        $modulePath = "{$this->basePath}/{$this->moduleName}";

        // Garante criação com permissão
        if (!is_dir($modulePath)) {
            if (mkdir($modulePath, 0777, true)) {
                chmod($modulePath, 0777);
            } else {
                echo "❌ Erro ao criar módulo: {$modulePath}\n";
                return;
            }
        }

        $moduleUc = ucfirst($this->moduleName);
        $cadastroBase = "{$modulePath}/pages/Cadastros/Cadastro{$moduleUc}";
       
        $folders = [
            "{$modulePath}/controllers",
            "{$modulePath}/models",
            "{$modulePath}/routes",
            
            $cadastroBase,
            "{$cadastroBase}/Form{$moduleUc}",
          
        ];
        
        foreach ($folders as $folder) {
            if (!is_dir($folder)) {
                if (mkdir($folder, 0777, true)) {
                    chmod($folder, 0777);
                    echo "📁 Criado: {$folder}\n";
                } else {
                    echo "❌ Erro ao criar pasta: {$folder}\n";
                }
            }
        }
        $this->setAllFilesToExecutable("{$this->basePath}/{$this->moduleName}");
        $this->setPermissionsRecursively("{$modulePath}/pages");
    }
    
   

    public function setPermissionsRecursively(string $path, int $permissions = 0777): void
    {
        if (!file_exists($path)) return;

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($items as $item) {
            chmod($item, $permissions);
        }

        chmod($path, $permissions);
        echo "✅ Permissões 0777 aplicadas recursivamente em: {$path}\n";
    }

    public function setAllFilesToExecutable(string $path): void
    {
        if (!file_exists($path)) {
            echo "❌ Caminho não encontrado: {$path}\n";
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $item) {
            if ($item->isFile()) {
                chmod($item->getRealPath(), 0755);
            }
        }

        echo "✅ Todos os arquivos em {$path} foram definidos como 0755\n";
    }
    
    public function generateController(?string $fileName = null): void
    {
        $controller = new ControllerGenerator($this->moduleName, $this->basePath, $fileName);
        $controller->masterKey = $this->masterKey;
        $controller->generate();
    }
    
    public function generateModel(): void
    {
        $model = new ModelGenerator($this->moduleName, $this->basePath);
        $model->masterKey = $this->masterKey;
        $model->setFields($this->fields);
        $model->setUpdateKey($this->updateKey);
        $model->setDeleteKey($this->deleteKey); 
        $model->generate();
    }

    public function generateForm(): void
    {
        $form = new FormGenerator($this->moduleName, $this->basePath);
        $form->generate();
    }

    public function generateTable(): void
    {
        $table = new TableGenerator($this->moduleName, $this->basePath);
        $table->generate();
    }
    public function generateEntryPage(): void
    {
        $entry = new EntryPageGenerator($this->moduleName, $this->basePath);
        $entry->generate();
    }
    public function generateRootFiles(): void
    {
        $root = new \App\Generator\Generators\RootFilesGenerator($this->moduleName, $this->basePath);
        $root->generate();
    }

}
