<?php

namespace App\Generator\Generators;

class ModelGenerator
{
    private string $moduleName;
    private string $basePath;
    public array $fields;
    public int $space = 4;
    public string $spaceIdent;
    private bool $createFilter = true;
    public  $updateKey;
    public  $deleteKey;
    public $masterKey;

    public function __construct(string $moduleName, string $basePath)
    {
        $this->moduleName = $moduleName;
        $this->basePath = rtrim($basePath, '/');
        $this->spaceIdent = str_repeat(' ', $this->space); // Espaçamento padrão de 4 espaços
    }

    public function setFields($field){
        $this->fields = $field;
    }
    public function setUpdateKey($k){
        $this->updateKey = $k;
    }
    public function setDeleteKey($k){
        $this->deleteKey = $k;
    }

    private function Imports(){
        $imports = [
            'import { ConfiguracaoPaginacao, ResponseGeral } from "@/lib/utils/index.types";'
        ];
        return implode("\n", $imports) . "\n\n";
    }

    private function QueryKey(): string
    {
        $kebab = strtolower(preg_replace('/(?<!^)([A-Z])/', '-$1', $this->moduleName));
        return "export enum QueryKeys { 
            {$this->moduleName}List = '{$kebab}'
        }\n";
    }

    private function ListRequest(): string
    {
        $kebab = strtolower(preg_replace('/(?<!^)([A-Z])/', '-$1', $this->moduleName));
        return "export interface {$this->moduleName}ListRequest {
            paginacao: ConfiguracaoPaginacao;
        }\n";
    }

    private function ListResponse(): string
    {
        $kebab = strtolower(preg_replace('/(?<!^)([A-Z])/', '-$1', $this->moduleName));
        return "export type {$this->moduleName}ListResponse = ResponseGeral<{$this->moduleName}[]> \n\n";
    }

    private function generateInterfaceContent(): string
    {
        $interfaceName = ucfirst($this->moduleName);
        $lines = ["export interface {$interfaceName} {"];
        if($this->createFilter){
            foreach ($this->fields as $field => $type) {
                $lines[] = "{$this->spaceIdent}{$field}: {$type};";
            }
        }
        $lines[] = "}";
        $this->enableCreateFilter();
        return implode("\n", $lines) . "\n";
    }

    private function createOrUpdateMutationOptions(string $type): string
    {
        return implode("\n", [
            "export type " . ucfirst($this->moduleName) .$type."MutationOptions = {
                onSuccess?: () => void;
                onError?: (error: Error) => void;
            };",
        ]) . "\n";
    }

    private function generateInterfaceCreateOrUpdateOrDeleteRequestResponse(string $type): string
    {
        $interfaceName = ucfirst($this->moduleName);
        $lines = ["export type {$interfaceName}".ucfirst($type)." = {"];
        
        if($type === 'CreateOrUpdateRequest') {      
            foreach ($this->fields as $field => $type) {
                if (array_key_exists($field, $this->updateKey[0])) 
                    $lines[] = "{$this->spaceIdent}{$field}: {$type};";
            }
        }
        if($type === 'CreateOrUpdateResponse') {
            foreach ($this->fields as $field => $type) {
                if (array_key_exists($field, $this->updateKey[1])) 
                $lines[] = "{$this->spaceIdent}{$field}: {$type};";
            }
        }

        if($type === 'DeleteRequest') {      
            foreach ($this->fields as $field => $type) {
                if (array_key_exists($field, $this->deleteKey[0])) 
                    $lines[] = "{$this->spaceIdent}{$field}: {$type};";
            }
        }
        if($type === 'DeleteResponse') {
            foreach ($this->fields as $field => $type) {
                if (array_key_exists($field, $this->deleteKey[1])) 
                $lines[] = "{$this->spaceIdent}{$field}: {$type};";
            }
        }
        
        $lines[] = "}";
        $this->enableCreateFilter();
        return implode("\n", $lines) . "\n";
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

        $content = $this->Imports()."\n";
        $content .= $this->QueryKey()."\n";
        $content .= $this->ListRequest()."\n";
        $content .= $this->ListResponse();
        $content .= $this->generateInterfaceContent()."\n";
        $content .=$this->createOrUpdateMutationOptions('CreateOrUpdate')."\n";
        
        
        $content .= $this->generateInterfaceCreateOrUpdateOrDeleteRequestResponse('CreateOrUpdateRequest')."\n";
        $content .= $this->generateInterfaceCreateOrUpdateOrDeleteRequestResponse('CreateOrUpdateResponse')."\n";
        
        $content .=$this->createOrUpdateMutationOptions('Delete')."\n";
       
        //request
        if(empty($this->deleteKey[0])) $this->disabelCreateFilter();
        $content .= $this->generateInterfaceCreateOrUpdateOrDeleteRequestResponse('DeleteRequest')."\n";
        //response
        if(empty($this->deleteKey[1])) $this->disabelCreateFilter();
        $content .= $this->generateInterfaceCreateOrUpdateOrDeleteRequestResponse('DeleteResponse')."\n";
           
            
       
       
        
        $content .= "\n";
        
       
        ///$content .= $this->generateInterfaceContentCriar();


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




    public function disabelCreateFilter(): void
    {
        $this->createFilter = false;
    }
    public function enableCreateFilter(): void
    {
        $this->createFilter = true;
    }
}
