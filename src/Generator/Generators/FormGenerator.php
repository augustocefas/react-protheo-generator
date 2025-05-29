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
        $formFolder = "FormCadastro{$this->moduleName}";
        $dir = "{$this->basePath}/{$this->moduleName}/pages/Cadastros/{$this->moduleName}/{$formFolder}";

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }

        $files = [
            "{$formFolder}.tsx" => <<<TSX
// Auto-gerado por CrudGenerator
import { use{$formFolder} } from "./{$formFolder}.hook";

export default function {$formFolder}() {
  const { formData } = use{$formFolder}();

  return (
    <form>
      <h2>Formulário de {$this->moduleName}</h2>
      {/* Campos do formulário aqui */}
    </form>
  );
}
TSX,

            "{$formFolder}.hook.ts" => <<<TS
// Auto-gerado por CrudGenerator

export function use{$formFolder}() {
  const formData = {};

  return { formData };
}
TS,

            "{$formFolder}.schema.ts" => <<<TS
// Auto-gerado por CrudGenerator

export const {$formFolder}Schema = {
  // Definição do schema de validação
};
TS,

            "{$formFolder}.types.ts" => <<<TS
// Auto-gerado por CrudGenerator

export type {$formFolder}Data = {
  // Definição dos tipos para o formulário
};
TS,

            "{$formFolder}.utils.ts" => <<<TS
// Auto-gerado por CrudGenerator

export function format{$this->moduleName}Data(data: any) {
  // Lógica de formatação
  return data;
}
TS,
        ];

        foreach ($files as $fileName => $content) {
            $path = "{$dir}/{$fileName}";
            file_put_contents($path, $content);
            chmod($path, 0755);
        }

        echo "✅ Formulário completo gerado em: {$dir}\n";
    }
}
