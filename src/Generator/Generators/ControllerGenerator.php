<?php

namespace App\Generator\Generators;

class ControllerGenerator
{
    private string $moduleName;
    private string $basePath;
    public $masterKey;
    public function __construct(string $moduleName, string $basePath, ?string $controllerFileName = null)
    {
        $this->moduleName = $moduleName;
        $this->basePath = rtrim($basePath, '/');
    }

    private function generateDefaultFileName(string $name): string
    {
        $kebab = strtolower(preg_replace('/(?<!^)([A-Z])/', '-$1', $name));
        return "cadastro-{$kebab}.ts";
    }

    private function generateControllerContent(): string
    {
        $pascal = ucfirst($this->moduleName);
        $kebab = strtolower(preg_replace('/(?<!^)([A-Z])/', '-$1', $this->moduleName));
        $typesFile = "cadastro-{$kebab}.types";

        return <<<TS
import { useMutation, useQuery } from "@tanstack/react-query"
import {
    {$pascal}CreateOrUpdateMutationOptions,
    {$pascal}CreateOrUpdateRequest,
    {$pascal}CreateOrUpdateResponse,
    {$pascal}DeleteMutationOptions,
    {$pascal}DeleteRequest,
    {$pascal}DeleteResponse,
    {$pascal}ListRequest,
    {$pascal}ListResponse,
    QueryKeys,
} from "../models/{$typesFile}"
import { api } from "@/lib/config/axios"
import { toast } from "sonner"
import { ResponseGeral } from "@/lib/utils/index.types"
import { proteoQueryClient } from "@/lib/config/react-query"

export const use{$pascal}List = (request: {$pascal}ListRequest) => {
    return useQuery({
        queryKey: [QueryKeys.{$pascal}List, request],
        queryFn: async () => {
            const { data } = await api.get<{$pascal}ListResponse>(
                "/{$kebab}/list"
            )
            if (!data.sucesso) toast.error("Não foi possível obter os itens.")

            return data
        },
    })
}

export const use{$pascal}CreateMutation = (
    options?: {$pascal}CreateOrUpdateMutationOptions
) => {
    return useMutation({
        mutationFn: async (request: {$pascal}CreateOrUpdateRequest) => {
            const { data } = await api.post<ResponseGeral<{$pascal}CreateOrUpdateResponse>>(
                "/{$kebab}", request
            )
            return data
        },
        onSuccess: () => {
            proteoQueryClient.invalidateQueries({
                queryKey: [QueryKeys.{$pascal}List],
                exact: false,
            })
            if (options?.onSuccess) options.onSuccess()
        },
        onError: () => {
            toast.error("Não foi possível adicionar o item.")
        },
    })
}

export const use{$pascal}UpdateMutation = (
    options?: {$pascal}CreateOrUpdateMutationOptions
) => {
    return useMutation({
        mutationFn: async (request: {$pascal}CreateOrUpdateRequest) => {
            const { data } = await api.put<ResponseGeral<{$pascal}CreateOrUpdateResponse>>(
                "/{$kebab}", request
            )
            return data
        },
        onSuccess: () => {
            proteoQueryClient.invalidateQueries({
                queryKey: [QueryKeys.{$pascal}List],
                exact: false,
            })
            if (options?.onSuccess) options.onSuccess()
        },
        onError: () => {
            toast.error("Não foi possível atualizar o item.")
        },
    })
}

export const use{$pascal}DeleteMutation = (
    options?: {$pascal}DeleteMutationOptions
) => {
    return useMutation({
        mutationFn: async (request: {$pascal}DeleteRequest) => {
            const { data } = await api.delete<ResponseGeral<{$pascal}DeleteResponse>>(
                `/{$kebab}/\${request.{$this->masterKey}}`, {
                    data: request,
                }
            )
            return data
        },
        onSuccess: () => {
            proteoQueryClient.invalidateQueries({
                queryKey: [QueryKeys.{$pascal}List],
                exact: false,
            })
            if (options?.onSuccess) options.onSuccess()
        },
        onError: () => {
            toast.error("Não foi possível remover o item.")
        },
    })
}
TS;
    }

    public function generate(): void
    {
        $dir = "{$this->basePath}/{$this->moduleName}/controllers";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $fileName = $this->generateDefaultFileName($this->moduleName);
        $filePath = "{$dir}/{$fileName}";
        $content = $this->generateControllerContent();

        if (file_put_contents($filePath, $content)) {
            chmod($filePath, 0755);
            echo "✅ Controller criado: {$filePath}\n<br>";
        } else {
            echo "❌ Falha ao criar controller em: {$filePath}\n<br>";
        }
    }
}
