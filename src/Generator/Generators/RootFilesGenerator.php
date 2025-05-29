<?php

namespace App\Generator\Generators;

class RootFilesGenerator
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
        $dir = "{$this->basePath}/{$this->moduleName}";

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }

        // index.tsx
        $indexContent = <<<TSX
// Auto-gerado por CrudGenerator

export default function {$this->moduleName}Index() {
  return (
    <div>
      <h1>Módulo {$this->moduleName}</h1>
    </div>
  );
}
TSX;

        file_put_contents("{$dir}/index.tsx", $indexContent);
        chmod("{$dir}/index.tsx", 0755);

        // pages.ts
        $pagesContent = <<<TS
// Auto-gerado por CrudGenerator

export const {$this->moduleName}Pages = [
  // Adicione suas rotas aqui
];
TS;

        file_put_contents("{$dir}/pages.ts", $pagesContent);
        chmod("{$dir}/pages.ts", 0755);

        echo "✅ Arquivos index.tsx e pages.ts gerados na raiz de {$this->moduleName}\n";
    }
}
