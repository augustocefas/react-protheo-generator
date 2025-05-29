<?php include "config.php";

    

   
    $nomeDoModulo = 'PremioDireto';

    $campos = [
        'id' => 'number',
        'nomeProduto' => 'string',
        'codProdutoOrigem' => 'string',
    ];
    
    use App\Generator\CrudGenerator;

    $crud = new CrudGenerator($nomeDoModulo, ROOT.DS.'output', $campos);
    $crud -> setFields($campos);
    echo $crud->basePath;

       $crud->createModuleFolders();
        
       //gera controller
       $crud->generateController();
       //gera model
       $crud->generateModel();

       $crud->generateEntryPage();

      //gera form
       $crud->generateForm();
       //gera table
       $crud->generateTable();
       $crud->generateRootFiles();
    
       