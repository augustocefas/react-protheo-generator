<?php include "config.php";

    

   
    $nomeDoModulo = 'PremioDireto';
    
    use App\Generator\CrudGenerator;

    $crud = new CrudGenerator($nomeDoModulo, ROOT.DS.'output');
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
    
       