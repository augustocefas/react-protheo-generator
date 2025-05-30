<?php

namespace App\Generator\Generators;

class FormGenerator
{
    private string $moduleName;
    private string $basePath;

    public function __construct(string $moduleName, string $basePath)
    {
        $this->moduleName = ucfirst($moduleName);
        $this->basePath = rtrim($basePath, '/');
    }

    public function generate(): void
    {
      $module = $this->moduleName;
      $dir = "{$this->basePath}/{$module}/pages/Cadastros/Cadastro{$module}/Form{$module}";
      $fileName = "/{$module}.tsx";
      $filePath = "{$dir}/{$fileName}";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }

        $file = "{$dir}/Form{$module}.tsx";
        $hook = "{$dir}/Form{$module}.hook.tsx";
        $schema = "{$dir}/Form{$module}.schema.tsx";
        $types = "{$dir}/Form{$module}.types.tsx";
        $utils = "{$dir}/Form{$module}.utils.tsx";
        
        $this->form($file);
        $this->createHook($hook);
        $this->createSchema($schema);
        $this->formTypes($types);
        $this->formUtils($utils);
        
    }

    public function createFile($filePath, $content): void
    {
      if (file_put_contents($filePath, $content)) {
        chmod($filePath, 0755);
        echo "✅ Página de entrada criada: {$filePath}\n<br>";
    } else {
        echo "❌ Erro ao criar a página de entrada: {$filePath}\n<br>";
    }
    }

    public function createHook(string $caminho): void
    {
      $content = "//modelo de hook do form {$this->moduleName}\n";
      $this->createFile($caminho, $content);
    }
    public function createSchema(string $caminho): void
    {
      $content = "//modelo de hook do form {$this->moduleName}\n";
      $this->createFile($caminho, $content);
    }
    public function form(string $caminho): void
    {
       $content = "//modelo de hook do form {$this->moduleName}\n";
      $this->createFile($caminho, $content);
    }
    public function formTypes(string $caminho): void
    {
      $content = "//modelo de hook do form {$this->moduleName}\n";
      $this->createFile($caminho, $content);
    }
    public function formUtils(string $caminho): void
    {
      $content = "//modelo de hook do form {$this->moduleName}\n";
      $this->createFile($caminho, $content);
    }


        
}
