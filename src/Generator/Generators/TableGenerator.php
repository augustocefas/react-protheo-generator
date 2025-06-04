<?php

namespace App\Generator\Generators;

class TableGenerator
{
    private string $moduleName;
    private string $basePath;

    public string $nomePastaTabela;
    private string $nomeTabela;
    public $masterKey;
    public $fields;

    public function __construct(string $moduleName, string $basePath)
    {
        $this->moduleName = ucfirst($moduleName);
        $this->basePath = rtrim($basePath, '/');
    }

    public function setFields(array $fields): void
    {
        if (empty($fields)) {
            throw new \InvalidArgumentException("Os campos não podem ser vazios.");
        }
        $this->fields = $fields;
    }

    public function generate(): void
    {
        $this->nomePastaTabela = "TabelaCadastro{$this->moduleName}";
        $dir = "{$this->basePath}/{$this->moduleName}/pages/Cadastros/Cadastro{$this->moduleName}/{$this->nomePastaTabela}";

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }

        $fileTabela = "{$dir}/{$this->nomePastaTabela}.tsx";
        $fileHook = "{$dir}/{$this->nomePastaTabela}.hook.tsx";
        $fileUtils = "{$dir}/{$this->nomePastaTabela}.utils.tsx";

        $this->hookCreate($fileHook);
        $this->tableCreate($fileTabela);
        $this->utilsCreate($fileUtils);

        echo "✅ Tabela criada em: {$dir}\n";
    }

    public function utilsCreate(string $fileUtils): void
    {
        file_put_contents($fileUtils, "// utils {$this->moduleName}\n");
        chmod($fileUtils, 0755);
    }

    public function hookCreate(string $fileHook): void
    {
        $kebabName = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $this->moduleName));

        $colunas = [];
        foreach ($this->fields as $field => $type) {
            $render = '';
            $titulo = $options['table']['titulo'] ?? $this->humanize($field);
        
            $colunas[] = <<<TS
        {
            headerName: "{$titulo}",
            field: "{$field}",
            flex: 1{$render}
        }
TS;
}
        

        $colunas[] = <<<TS
        {
            headerName: "Ações",
            field: "actions",
            type: "actions",
            width: 150,
            getActions: (params: GridRowParams) => [
                <GridActionsCellItem
                    icon={<Edit01 width={20} height={20} />}
                    onClick={() => cadastroModal.set({ nome: "EDITAR", dadosIniciais: params.row })}
                    label="Editar"
                />,
                <GridActionsCellItem
                    icon={<Trash01 width={22} height={22} />}
                    onClick={() => cadastroDialogs.set({ nome: "ITEM_EXCLUIR", dados: params.row })}
                    label="Excluir"
                />,
            ],
        }
TS;

        $colunasString = implode(",\n", $colunas);

        $contentHook = <<<TSX
import {
    use{$this->moduleName}DeleteMutation,
    use{$this->moduleName}List,
} from "@/modulos/resseguro/controllers/cadastro-{$kebabName}"
import { DataGridColunas, DataGridLinhas } from "@/components/DataGrid/DataGrid.types"
import { useState } from "react"
import { GridActionsCellItem, GridRowParams } from "@mui/x-data-grid"
import { Edit01, Trash01 } from "@/assets/gallery"
import { useCadastroAtom } from "@/modulos/resseguro/atoms/cadastros.atom"
import { {$this->moduleName} } from "@/modulos/resseguro/models/cadastro-{$kebabName}.types"

export const useTabela{$this->moduleName} = () => {
    const { modal: cadastroModal, dialog: cadastroDialogs } = useCadastroAtom()

    const deleteMutation = use{$this->moduleName}DeleteMutation({
        onSuccess: () => {
            cadastroModal.set(null)
            cadastroDialogs.set({ nome: "ITEM_EXCLUIDO", dados: null })
        },
    })

    const handleDelete = () => {
        if (cadastroDialogs.data?.dados) {
            deleteMutation.mutate(cadastroDialogs.data?.dados as {$this->moduleName})
        }
    }

    const colunas: DataGridColunas = [
{$colunasString}
    ]

    const [paginaAtual, setPaginaAtual] = useState(1)
    const listQuery = use{$this->moduleName}List({
        paginacao: {
            pagina: paginaAtual,
            indexInicial: 1,
            tamanhoPagina: 6,
        },
    })

    const linhas: DataGridLinhas = listQuery?.data?.data || []
    const quantidadePaginas = listQuery.data?.paginacao?.quantidadeTotalPaginas || 1

    const getRowId = (row: {$this->moduleName}) => {
        return row.{$this->masterKey}
    }

    return {
        linhas,
        colunas,
        getRowId,
        isLoading: listQuery.isLoading || listQuery.isFetching,
        paginaAtual,
        setPaginaAtual,
        quantidadePaginas,
        cadastroModal,
        cadastroDialogs,
        handleDelete,
    }
}
TSX;

        file_put_contents($fileHook, $contentHook);
        chmod($fileHook, 0755);
    }

    public function tableCreate($fileTabela)
    {
        $contentTabela = <<<TSX
import { DataGrid } from "@/components/DataGrid"
import { useTabela{$this->moduleName} } from "./{$this->nomePastaTabela}.hook"
import { ExcluirItem } from "../../_components/modals/ExcluirItem/ExcluirItem"
import { ItemExcluido } from "../../_components/modals/ItemExcluido/ItemExcluido"

export const {$this->nomePastaTabela} = () => {
    const {
        linhas,
        colunas,
        getRowId,
        isLoading,
        paginaAtual,
        quantidadePaginas,
        setPaginaAtual,
        cadastroDialogs,
        handleDelete,
    } = useTabela{$this->moduleName}()

    return (
        <>
            <DataGrid
                rows={linhas}
                columns={colunas}
                getRowId={getRowId}
                loading={isLoading}
                paginacao={{ paginaAtual, quantidadePaginas, setPaginaAtual }}
            />
            <ExcluirItem
                open={cadastroDialogs.data?.nome === "ITEM_EXCLUIR"}
                onConfirmExcluir={handleDelete}
                handleFechar={() => cadastroDialogs.set(null)}
            />
            <ItemExcluido
                open={cadastroDialogs.data?.nome === "ITEM_EXCLUIDO"}
                handleFechar={() => cadastroDialogs.set(null)}
            />
        </>
    )
}
TSX;

        file_put_contents($fileTabela, $contentTabela);
        chmod($fileTabela, 0755);
    }

    private function humanize(string $input): string
    {
        return ucfirst(preg_replace('/(?<!^)[A-Z]/', ' $0', $input));
    }
}
