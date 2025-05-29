<?php

namespace App\Generator\Generators;

class EntryPageGenerator
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
        $dir = "{$this->basePath}/{$this->moduleName}/pages/Cadastros/Cadastro{$this->moduleName}";
        $file = "{$dir}/Cadastro{$this->moduleName}.tsx";

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $content = <<<TSX
// Auto-gerado por CrudGenerator

import React from 'react';
import FormCadastro{$this->moduleName} from './FormCadastro{$this->moduleName}/FormCadastro{$this->moduleName}';
import TabelaCadastro{$this->moduleName} from './TabelaCadastro{$this->moduleName}/TabelaCadastro{$this->moduleName}';

export default function Cadastro{$this->moduleName}() {
  return (
    <div>
      <h1>Cadastro de {$this->moduleName}</h1>
      <FormCadastro{$this->moduleName} />
      <TabelaCadastro{$this->moduleName} />
    </div>
  );
}
TSX;

        if (file_put_contents($file, $content)) {
            echo "✅ Página principal criada: {$file}<br>";
        } else {
            echo "❌ Falha ao criar página principal: {$file}<br>";
        }
    }
}
