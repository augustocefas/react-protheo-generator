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
    $module = $this->moduleName;
    $dir = "{$this->basePath}/{$module}/pages/Cadastros/{$module}";
    $fileName = "/{$module}.tsx";
    $filePath = "{$dir}/{$fileName}";

    
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
        chmod($dir, 0777);
    }

    $content = <<<TSX
import { Stack, Typography } from "@mui/material"
import { Row } from "@/components/Grid/Row/Row"
import { Col } from "@/components/Grid/Col/Col"
import { Button } from "@/components/Button"
import { Plus } from "@/assets/gallery"
import { TabelaCadastro{$module} } from "./TabelaCadastro{$module}/TabelaCadastro{$module}"
import { useCadastroAtom } from "@/modulos/resseguro/atoms/cadastros.atom"
import { FormCadastro{$module} } from "./FormCadastro{$module}/FormCadastro{$module}"

export const Cadastro{$module} = () => {
    const { modal: cadastroModal } = useCadastroAtom()

    return (
        <Stack>
            <Row alignItems="center" mb={2}>
                <Col size="grow">
                    <Typography variant="text_18_semibold">{$module}</Typography>
                </Col>
                <Col>
                    <Button
                        tamanho="md"
                        onClick={() => cadastroModal.set({ nome: "ADICIONAR" })}
                    >
                        <Plus /> Adicionar
                    </Button>
                </Col>
            </Row>

            <TabelaCadastro{$module} />
            <FormCadastro{$module} open={!!cadastroModal.data?.nome} />
        </Stack>
    )
}
TSX;

    if (file_put_contents($filePath, $content)) {
        chmod($filePath, 0755);
        echo "✅ Página de entrada criada: {$filePath}\n";
    } else {
        echo "❌ Erro ao criar a página de entrada: {$filePath}\n";
    }
}

}
