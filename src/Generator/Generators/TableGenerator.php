<?php

namespace App\Generator\Generators;

class TableGenerator
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
        $nomePastaTabela = "TabelaCadastro{$this->moduleName}";
        $dir = "{$this->basePath}/{$this->moduleName}/pages/Cadastros/Cadastro{$this->moduleName}/{$nomePastaTabela}";

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }

        // Arquivo principal da tabela
        $fileTabela = "{$dir}/{$nomePastaTabela}.tsx";
        $fileHook = "{$dir}/{$nomePastaTabela}.hook.tsx";

        $contentTabela = <<<TSX
// Auto-gerado por CrudGenerator
import { use{$this->moduleName}Table } from "./{$nomePastaTabela}.hook";

export default function {$nomePastaTabela}() {
  const data = use{$this->moduleName}Table();

  return (
    <div>
      <h1>Tabela de {$this->moduleName}</h1>
      {/* Renderização da tabela aqui */}
    </div>
  );
}
TSX;

        $contentHook = <<<TS
// Auto-gerado por CrudGenerator

export function use{$this->moduleName}Table() {
  // lógica de hook da tabela
  return [];
}
TS;

        file_put_contents($fileTabela, $contentTabela);
        chmod($fileTabela, 0755);

        file_put_contents($fileHook, $contentHook);
        chmod($fileHook, 0755);

        echo "✅ Tabela criada em: {$dir}\n";
    }
}
