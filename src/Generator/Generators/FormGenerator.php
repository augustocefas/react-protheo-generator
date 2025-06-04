<?php

namespace App\Generator\Generators;

class FormGenerator
{
    private string $moduleName;
    private string $basePath;

    public  $updateKey;
    public  $deleteKey;
    public $masterKey;
    public  $fields;

    public function __construct(string $moduleName, string $basePath)
    {
        $this->moduleName = ucfirst($moduleName);
        $this->basePath = rtrim($basePath, '/');
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

    public function generate(): void
    {
      $module = $this->moduleName;
      $dir = "{$this->basePath}/{$module}/pages/Cadastros/Cadastro{$module}/FormCadastro{$module}";

        echo '<br><br>';
        echo($dir);
        echo '<br><br>';


        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }

        $file = "{$dir}/FormCadastro{$module}.tsx";
        $hook = "{$dir}/FormCadastro{$module}.hook.tsx";
        $schema = "{$dir}/FormCadastro{$module}.schema.tsx";
        $types = "{$dir}/FormCadastro{$module}.types.ts";
        $utils = "{$dir}/FormCadastro{$module}.utils.tsx";
        
        $this->form($file);
        $this->createHook($hook);
        $this->createSchema($schema);
        $this->formTypes($types);
        $this->formUtils($utils);
        
    }

    public function createFile($filePath, $content): void
    {
      if (file_put_contents($filePath, $content)) {
        chmod($filePath, 0755);
        echo "✅ Página de entrada criada: {$filePath}\n\n";
    } else {
        echo "❌ Erro ao criar a página de entrada: {$filePath}\n\n";
    }
    }

    //utils


    public function createHook(string $caminho): void
    {
      $kebab = strtolower(preg_replace('/(?<!^)([A-Z])/', '-$1', $this->moduleName));
      $pascal = ucfirst($this->moduleName);

        $requiredConditions = [];
        foreach ($this->fields as $field => $config) {
            if (!empty($config['required'])) {
                $requiredConditions[] = "!isEmpty(formState.{$field})";
            }
        }
        $conditionsStr = implode(" ||\n            ", $requiredConditions);

        $handleCloseWithConfirmation = <<<TS
        const handleCloseWithConfirmation = () => {
            const hasChanges =
            {$conditionsStr};
            if (hasChanges) {
                cadastroDialogs.set({ nome: "ALTERACOES_NAO_SALVAS", dados: { onConfirm: () => cadastroModal.set(null) } });
            } else {
                cadastroModal.set(null);
            }
        };
    TS;
        $initialState = '';
        $content = '';
        foreach($this->fields as $key => $field) {
            $content.= "    \"{$key}\": {$field['type']},\n";
            if($field['inputType'] == 'text' && $field['required']){
                $initialState.= "{$key}: \"\",\n";
            }
            if($field['inputType'] == 'number' && $field['required']){
                $initialState.= "{$key}: 0,\n";
            }
        }

        $content = <<<TS
        import { SyntheticEvent, useEffect, useState } from "react"
        import { validationSchema } from "./FormCadastro{$pascal}.schema"
        import { FormState, NormalizedFormState } from "./FormCadastro{$pascal}.types"
        import { useCadastroAtom } from "@/modulos/resseguro/atoms/cadastros.atom"
        import { isEmpty } from "lodash"
       
        import {
          normalizedToSubmit,
          normalizeFormState,
          normalizeInitialEditState,
        } from "./FormCadastro{$pascal}.utils"
        import {
          use{$pascal}CreateMutation,
          use{$pascal}UpdateMutation,
          use{$pascal}List,
        } from "@/modulos/resseguro/controllers/cadastro-{$kebab}"
        import { obterErrosDoEschemaValidado } from "@/lib/utils/yup"
        import { {$pascal}} from "@/modulos/resseguro/models/cadastro-{$kebab}.types"

        {$handleCloseWithConfirmation}

        type Adicionar{$pascal}Props = { open: boolean }
        const emptyState: FormState = {
            {$initialState}
        }

        export const useForm{$pascal} = ({ open }: Adicionar{$pascal}Props) => {
          const { modal: cadastroModal, dialog: cadastroDialogs } = useCadastroAtom()
            
          const dadosIniciais = cadastroModal.data?.dadosIniciais as any
          const isEditing = !isEmpty(dadosIniciais)
          const lista{$pascal}Query = use{$pascal}List();
          const {$pascal}lista = lista{$pascal}Query.data?.data || []
          
           const handleChangeRamo = (evt: SyntheticEvent, ramo: Ramo | null) => {
                setFormState((prev) => ({ ...prev, ramo }))
           }
          
          const [formState, setFormState] = useState<FormState>({ ...emptyState })

          const createHandler =
            (key: keyof FormState) =>
            ({ target }: SyntheticEvent) => {
              setFormState((prev) => ({ ...prev, [key]: (target as HTMLInputElement).value }))
            }

          const [formErrors, setFormErrors] = useState<Record<string, string>>({})

          const item{$pascal}CreateMutation = use{$pascal}CreateMutation({
            onSuccess: () => {
              cadastroModal.set(null)
              cadastroDialogs.set({ nome: "ITEM_ADICIONADO", dados: null })
            },
          })

          const item{$pascal}UpdateMutation = use{$pascal}UpdateMutation({
          onSuccess: () => {
              cadastroModal.set(null)
              cadastroDialogs.set({ nome: "ITEM_ALTERADO", dados: null })
            },
          })

          const handleSubmit{$pascal} = () => {
            validationSchema
            .validate(normalizeFormState(formState), { abortEarly: false })
            .then((data: NormalizedFormState) => {
                if (isEditing) {
                    return item{$pascal}UpdateMutation.mutate(normalizedToSubmit(data))
                }
                item{$pascal}CreateMutation.mutate(normalizedToSubmit(data))
            })
            .catch((erro) => setFormErrors(obterErrosDoEschemaValidado(erro)))
          }

           
                 
          useEffect(() => {
            if (!open) setFormState({ ...emptyState }) 
            if (dadosIniciais) {
              setFormState(normalizeInitialEditState(dadosIniciais))
              return
            }
          }, [dadosIniciais, open])

          return {
            isEditing,
            cadastroModal,
            cadastroDialogs,
            {$pascal}lista,
            createHandler,
            formState,
            formErrors,
            handleSubmit{$pascal},
            handleChange{$pascal},
            handleCloseWithConfirmation
          }
        };
      TS;
      $this->createFile($caminho, $content);
    }
    public function createSchema(string $caminho): void
    {
      $content = 'import * as Yup from "yup"'. "\n\n";

        foreach ($this->fields as $field => $config) {

            if($config['required']){
                $fieldError = preg_replace('/([a-z])([A-Z])/', '$1_$2', $field);
                $strToUpper = strtoupper($fieldError);
                $content.= "const _{$strToUpper}_ERROR = \"Error {$field}\";\n";
            }
        }

      $content.= "\n\nexport const validationSchema = Yup.object().shape({\n";

        foreach ($this->fields as $field => $config) {
            $type = $config['type'] ?? 'string';
            $isRequired = $config['required'] ?? false;
            $label = $config['table']['titulo'] ?? ucfirst($field);

            $line = "  {$field}: Yup.{$type}()";
            $line .= ".typeError(\"{$label} deve ser um " . ($type === 'string' ? 'texto' : 'número') . "\")";

            if ($isRequired) {
                $fieldError = preg_replace('/([a-z])([A-Z])/', '$1_$2', $field);
                $strToUpper = strtoupper($fieldError);
                $line .= ".required(_{$strToUpper}_ERROR)";
            }

            $line .= ",\n";
            $content .= $line;
        }
        $content .= "});\n";
      $this->createFile($caminho, $content);
    }

    public function form(string $caminho): void
    {
      $pascal = ucfirst($this->moduleName);


      $fields = '';

        foreach ($this->fields as $field => $item) {
            $fieldError = '';

            if ($item['required'] === true) {
                $fieldError = <<<TS
{formErrors['{$field}'] && (
    <Typography variant="caption" color="error" mt={0.5}>
        {formErrors['{$field}']}
    </Typography>
)}
TS;
            }

            if ($item['inputType'] === 'text') {
                $isRequired = $item['required'] ? '*' : '';
                $fields .= <<<TS
<Stack>
    <Typography
        component="label"
        variant="text_14_medium"
        mb={0.5}
        color="text.secondary"
    >
        {$item['table']['titulo']}{$isRequired}
    </Typography>
    <Input
        placeholder=""
        value={formState['{$field}']}
        onChange={createHandler('{$field}')}
        erro={formErrors['{$field}']}
        tamanho="md"
        padding="8px 12px"
    />
    {$fieldError}
</Stack>

TS;
            }
        }

      $content = <<<TS
        import React from "react"
        import { SlideOutMenu } from "@/components/SlideOutMenu/SlideOutMenu"
        import { Stack, Typography } from "@mui/material"       
        import { Adicionar{$pascal}Props } from "./FormCadastro{$pascal}.types"
        import { useForm{$pascal} } from "./FormCadastro{$pascal}.hook"
        import { Input } from "@/components/Input"
        import { Autocomplete } from "@/components/Autocomplete"
        import { AlteracoesNaoSalvas } from "../../_components/modals/AlteracoesNaoSalvas/AlteracoesNaoSalvas"
        import { AuxiliarInput } from "@/components/AuxiliarInput/"
        export const FormCadastro{$pascal}: React.FC<Adicionar{$pascal}Props> = ({ open }) => {\n
        
        const {
             isEditing,
            cadastroModal,
            cadastroDialogs,
            {$pascal}lista,
            createHandler,
            formState,
            formErrors,
            handleSubmit{$pascal},
            handleChange{$pascal},
            handleCloseWithConfirmation
        } = useForm{$pascal}({ open });

        return (
        <>
            <SlideOutMenu
                open={open}
                modo={cadastroModal.data?.nome || "ADICIONAR"}
                onClose={handleCloseWithConfirmation}
                onSave={handleSubmit{$pascal}}
            >
            <Stack gap={3}>
              <Stack>
                    {$fields}
              </Stack>
            </Stack>
            </SlideOutMenu>
            <AlteracoesNaoSalvas
                open={cadastroDialogs.data?.nome === "ALTERACOES_NAO_SALVAS"}
                handleFechar={() => cadastroDialogs.set(null)}
                onDiscard={() => {
                    cadastroDialogs.set(null)
                    cadastroModal.set(null)
                }}
            />
            <ItemAdicionado
                open={cadastroDialogs.data?.nome === "ITEM_ADICIONADO"}
                handleFechar={() => {
                    cadastroDialogs.set(null)
                    cadastroModal.set(null)
                }}
            />
            <ItemAlterado
                open={cadastroDialogs.data?.nome === "ITEM_ALTERADO"}
                handleFechar={() => {
                    cadastroDialogs.set(null)
                    cadastroModal.set(null)
                }}
            />
            </>)
        \n}\n
        TS;
      $this->createFile($caminho, $content);
    }



    public function formTypes(string $caminho): void
    {
      
      $content = 'export interface Adicionar' . $this->moduleName . 'Props {'."\n";
      $content.= "open: boolean;\n}\n";

      $content.= "export type FormState = {\n";
        foreach($this->fields as $key => $field) {
            $content.= "    {$key}: {$field['type']},\n";
        }
      $content.= "}\n\n";

      $content.= "export type NormalizedFormState = {\n";
        foreach($this->fields as $key => $field) {
            $content.= "    {$key}: {$field['type']},\n";
        }
      $content.= "}\n\n";

      $this->createFile($caminho, $content);
    }



    public function formUtils(string $caminho): void
    {
      $kebab = strtolower(preg_replace('/(?<!^)([A-Z])/', '-$1', $this->moduleName));
      $pascal = ucfirst($this->moduleName);

      $retorno = '';
        foreach($this->fields as $key => $field) {
            $retorno.= "{$key}: formState.{$key},\n";

        }
        
      $content = <<<TS
        import { FormState, NormalizedFormState } from "./FormCadastro{$pascal}.types"
        import {
          {$pascal},
          {$pascal}CreateOrUpdateRequest,
        } from "@/modulos/resseguro/models/cadastro-{$kebab}.types"
       

        export const normalizedToSubmit = (formState: NormalizedFormState): {$pascal}CreateOrUpdateRequest => {
          return {
            {$retorno}
          };
        };

        export const normalizeFormState = (formState: FormState): NormalizedFormState => {
          return {
            {$retorno}
          };
        };

        export const normalizeInitialEditState = (formState: {$pascal}): FormState => {
          return {
          {$retorno}
            }
           }
      TS;
      $this->createFile($caminho, $content);
    }


        
}
