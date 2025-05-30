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
      $module = $this->moduleName;
      $dir = "{$this->basePath}/{$module}/pages/Cadastros/Cadastro{$module}/Form{$module}";
      $fileName = "/{$module}.tsx";
      $filePath = "{$dir}/{$fileName}";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }

        $file = "{$dir}/Form{$module}.tsx";
        $hook = "{$dir}/Form{$module}.hook.tsx";
        $schema = "{$dir}/Form{$module}.schema.tsx";
        $types = "{$dir}/Form{$module}.types.ts";
        $utils = "{$dir}/Form{$module}.utils.tsx";
        
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
      $content = <<<TS
        import { SyntheticEvent, useEffect, useState } from "react"
        import { validationSchema } from "./Form{$pascal}.schema"
        import { FormState, NormalizedFormState } from "./Form{$pascal}.types"
        import { useCadastroAtom } from "@/modulos/resseguro/atoms/cadastros.atom"
        import { isEmpty } from "lodash"
        import { useDebounce } from "@/lib/hooks/useDebounce"
        import {
          normalizedToSubmit,
          normalizeFormState,
          normalizeInitialEditState,
        } from "./Form{$pascal}.utils"
        import {
          use{$pascal}CreateMutation,
          use{$pascal}UpdateMutation,
        } from "@/modulos/resseguro/controllers/cadastro-{$kebab}"
        import { obterErrosDoEschemaValidado } from "@/lib/utils/yup"

        type Adicionar{$pascal}Props = { open: boolean }
        const emptyState: FormState = {}

        export const useForm{$pascal} = ({ open }: Adicionar{$pascal}Props) => {
          const { modal: cadastroModal, dialog: cadastroDialogs } = useCadastroAtom()
            
          const dadosIniciais = cadastroModal.data?.dadosIniciais as any
          const isEditing = !isEmpty(dadosIniciais)

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
              //cadastroDialogs.set({ nome: "ITEM_ADICIONADO", dados: null })
            },
          })

          const item{$pascal}UpdateMutation = use{$pascal}UpdateMutation({
          onSuccess: () => {
              cadastroModal.set(null)
              //cadastroDialogs.set({ nome: "ITEM_ALTERADO", dados: null })
            },
          })

          const handleSubmit = () => {
            validationSchema
            .validate(normalizeFormState(formState), { abortEarly: false })
            .then((data: NormalizedFormState) => {
                if (isEditing) {
                    return {}
                }
                item{$pascal}CreateMutation.mutate(normalizedToSubmit(data))
            })
            .catch((erro) => setFormErrors(obterErrosDoEschemaValidado(erro)))
          }

          const handleCloseWithConfirmation = () => {}         
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
            createHandler,
            formState,
            formErrors,
            handleSubmit,
            handleCloseWithConfirmation
          }
        };
      TS;
      $this->createFile($caminho, $content);
    }
    public function createSchema(string $caminho): void
    {
      $content = 'import * as Yup from "yup"';
      $content.= "\n\nexport const validationSchema = Yup.object().shape({});\n";
      $this->createFile($caminho, $content);
    }

    public function form(string $caminho): void
    {
      $pascal = ucfirst($this->moduleName);
      $content = <<<TS
        import React from "react"
        import { SlideOutMenu } from "@/components/SlideOutMenu/SlideOutMenu"
        import { Stack, Typography } from "@mui/material"       
        import { Adicionar{$pascal}Props } from "./Form{$pascal}.types"
        import { useForm{$pascal} } from "./Form{$pascal}.hook"
        import { Input } from "@/components/Input"
        import { Autocomplete } from "@/components/Autocomplete"
        import { AlteracoesNaoSalvas } from "../../_components/modals/AlteracoesNaoSalvas/AlteracoesNaoSalvas"
        import { AuxiliarInput } from "@/components/AuxiliarInput/"
        export const Form{$pascal}: React.FC<Adicionar{$pascal}Props> = ({ open }) => {\n
        
        const {
          isEditing,
          cadastroModal,
          cadastroDialogs,
          createHandler,
          formState,
          formErrors,
          handleSubmit,
          handleCloseWithConfirmation
        } = useForm{$pascal}({ open });

        return (
        <>
            <SlideOutMenu
                open={open}
                modo={cadastroModal.data?.nome || "ADICIONAR"}
                onClose={handleCloseWithConfirmation}
                onSave={handleSubmit}
            >
            <Stack gap={3}>
              <Stack>
              
              </Stack>
            </Stack>
            </SlideOutMenu>
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
      $content.= "}\n\n";

      $content.= "export type NormalizedFormState = {\n";
      $content.= "}\n\n";

      $this->createFile($caminho, $content);
    }



    public function formUtils(string $caminho): void
    {
      $kebab = strtolower(preg_replace('/(?<!^)([A-Z])/', '-$1', $this->moduleName));
      $pascal = ucfirst($this->moduleName);
      $content = <<<TS
        import { FormState, NormalizedFormState } from "./Form{$pascal}.types"
        import {
          {$pascal},
          {$pascal}CreateOrUpdateRequest,
        } from "@/modulos/resseguro/models/cadastro-{$kebab}.types"
       

        export const normalizedToSubmit = (formState: NormalizedFormState): {$pascal}CreateOrUpdateRequest => {
          return {};
        };

        export const normalizeFormState = (formState: FormState): NormalizedFormState => {
          return {};
        };

        export const normalizeInitialEditState = ({dadosIniciais}: {$pascal}): FormState => {
          return {};
        };
      TS;
      $this->createFile($caminho, $content);
    }


        
}
