<?php

namespace App\Generator\Generators;

class ControllerGenerator
{
    private string $moduleName;
    private string $basePath;

    public function __construct(string $moduleName, string $basePath, ?string $controllerFileName = null)
    {
        $this->moduleName = $moduleName;
        $this->basePath = rtrim($basePath, '/');
    }

    private function generateDefaultFileName(string $name): string
{
    // Insere hífens antes de letras maiúsculas (exceto a primeira)
    $kebab = strtolower(preg_replace('/(?<!^)([A-Z])/', '-$1', $name));
    return "cadastro-{$kebab}.ts";
}

    public function generate(): void
    {
        $className = ucfirst($this->moduleName) . 'Controller';
        $dir = "{$this->basePath}/{$this->moduleName}/controllers";
        $fileName = $this->generateDefaultFileName($this->moduleName);
        $file = "{$dir}/{$fileName}";

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        

        $content = <<<TS
// Auto-gerado por CrudGenerator

export class {$className} {
  async list(req, res) {
    res.send('Listagem de {$this->moduleName}');
  }

  async create(req, res) {
    res.send('Criar {$this->moduleName}');
  }

  async update(req, res) {
    res.send('Atualizar {$this->moduleName}');
  }

  async delete(req, res) {
    res.send('Deletar {$this->moduleName}');
  }
}
TS;

        if (file_put_contents($file, $content)) {
            echo "<br>✅ Controller criado: {$file}<br>";
        } else {
            echo "<br>❌ Falha ao criar controller em: {$file}<br>";
        }
    }
}
