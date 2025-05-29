<?php

namespace App\Generator\Generators;

class ModelGenerator
{
    private string $moduleName;
    private string $basePath;

    public function __construct(string $moduleName, string $basePath)
    {
        $this->moduleName = $moduleName;
        $this->basePath = rtrim($basePath, '/');
    }

    public function generate(): void
    {
        $dir = "{$this->basePath}/{$this->moduleName}/models";

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $fileName = $this->generateDefaultFileName($this->moduleName);
        $file = "{$dir}/{$fileName}";

        $content = <<<TS
// Auto-gerado por CrudGenerator

export type {$this->getTypeName()} = {
  id: number;
  // TODO: adicionar os campos específicos do módulo {$this->moduleName}
};
TS;

        if (file_put_contents($file, $content)) {
            echo "✅ Model criado: {$file}<br>";
        } else {
            echo "❌ Falha ao criar model em: {$file}<br>";
        }
    }

    private function generateDefaultFileName(string $name): string
    {
        $kebab = strtolower(preg_replace('/(?<!^)([A-Z])/', '-$1', $name));
        return "cadastro-{$kebab}.types.ts";
    }

    private function getTypeName(): string
    {
        return ucfirst($this->moduleName);
    }
}
