<?php

namespace App\Generator\Generators;

class ModelGenerator
{
    private string $moduleName;
    private string $basePath;
    public array $fields;
    public int $space = 4;
    public string $spaceIdent;
    
    public function __construct(string $moduleName, string $basePath)
    {
        $this->moduleName = $moduleName;
        $this->basePath = rtrim($basePath, '/');
        $this->spaceIdent = str_repeat(' ', $this->space); // Espaçamento padrão de 4 espaços
    }

    public function setFields($field){
        $this->fields = $field;
    }

    private function createImports(){
        $imports = [
            'import { ConfiguracaoPaginacao, ResponseGeral } from "@/lib/utils/index.types";'
        ];
        return implode("\n", $imports) . "\n\n";
    }

    private function createQueryKey(): string
    {
        $kebab = strtolower(preg_replace('/(?<!^)([A-Z])/', '-$1', $this->moduleName));
        return "export enum QueryKeys { 
            Obter{$this->moduleName} = 'cadastro-{$kebab}'
        }\n";
    }

    private function createInterfaceListRequest(): string
    {
        $kebab = strtolower(preg_replace('/(?<!^)([A-Z])/', '-$1', $this->moduleName));
        return "export interface ListaCadastro{$this->moduleName}Request {
            paginacao: ConfiguracaoPaginacao;
        }\n";
    }

    private function createInterfaceListResponse(): string
    {
        $kebab = strtolower(preg_replace('/(?<!^)([A-Z])/', '-$1', $this->moduleName));
        return "export type ListaCadastro{$this->moduleName}Response = ResponseGeral<Cadastro{$this->moduleName}[]> \n\n";
    }

    private function generateInterfaceContentCadastro(): string
    {
        $interfaceName = 'Cadastro' . ucfirst($this->moduleName);
        $lines = ["export interface {$interfaceName} {"];
        
        foreach ($this->fields as $field => $type) {
            $lines[] = "{$this->spaceIdent}{$field}: {$type};";
        }
        $lines[] = "}";
        return implode("\n", $lines) . "\n";
    }

    private function generateInterfaceContentEditar(): string
    {
        return implode("\n", [
            "export type Editar" . ucfirst($this->moduleName) . "Request = Cadastro" . ucfirst($this->moduleName) . ";",
        ]) . "\n";
    }
    private function generateInterfaceContentCriar(): string
    {
        $interfaceBase = 'Cadastro' . ucfirst($this->moduleName);
        $typeName = 'Criar' . ucfirst($this->moduleName) . 'Request';

        if (array_key_exists('id', $this->fields)) {
            return "export type {$typeName} = Omit<{$interfaceBase}, 'id'>;\n";
        }

        return "export type {$typeName} = {$interfaceBase};\n";
    }


    public function generate(): void
    {
        $dir = "{$this->basePath}/{$this->moduleName}/models";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $fileName = "cadastro-" . strtolower(preg_replace('/(?<!^)([A-Z])/', '-$1', $this->moduleName)) . ".types.ts";
        $filePath = "{$dir}/{$fileName}";

        $content = $this->createImports()."\n";
        $content .= $this->createQueryKey()."\n";
        $content .= $this->createInterfaceListRequest()."\n";
        $content .= $this->createInterfaceListResponse()."\n";
        $content .= $this->generateInterfaceContentCadastro();
        $content .= "\n";
        $content .= $this->generateInterfaceContentEditar();
        $content .= $this->generateInterfaceContentCriar();


        if (file_put_contents($filePath, $content)) {
            chmod($filePath, 0755);
            echo "✅ Model gerado: {$filePath}\n";
        } else {
            echo "❌ Erro ao criar model: {$filePath}\n";
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
