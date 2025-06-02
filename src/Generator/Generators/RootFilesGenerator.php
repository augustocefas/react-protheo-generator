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
        $kebabName = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $this->moduleName));
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }

        // index.tsx
        $indexContent = <<<TSX
{
  path: "{$kebabName}",
  element: (
    <Paginas.resseguro.cadastros.Cadastro{$this->moduleName} />
  ),
}
TSX;

        file_put_contents("{$dir}/index.add.tsx.json", $indexContent);
        chmod("{$dir}/index.add.tsx.json", 0755);

        // pages.ts
        $pagesContent = <<<TS
//add to header file
import { Cadastro{$this->moduleName} } from "@/modulos/resseguro/pages/Cadastros/Cadastro{$this->moduleName}/Cadastro{$this->moduleName}"

//add to pages file
{
          
Cadastro{$this->moduleName},
      
}

TS;

        file_put_contents("{$dir}/page.add.ts,json", $pagesContent);
        chmod("{$dir}/page.add.ts,json", 0755);

        echo "✅ Arquivos index.tsx e pages.ts gerados na raiz de {$this->moduleName}\n\n";
    }
}
