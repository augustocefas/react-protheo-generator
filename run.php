<?php include "config.php";

    

   
    $nomeDoModulo = 'CoberturaDePara';

    $campos = [
        'codCoberturaPara' => 'number',
        'codCoberturaDe' => 'string',
    ];

    $masterKey = 'codCoberturaPara';

    $updateKey = [];
    $deleteKey = [];
    
    use App\Generator\CrudGenerator;

    $crud = new CrudGenerator($nomeDoModulo, ROOT.DS.'output', $campos);
        $crud -> setFields($campos);
        $crud -> setMasterKey($masterKey);
        $crud -> setDeleteUpdateKey($campos, $campos, 'update');
        $crud -> setDeleteUpdateKey($campos, [], 'delete');
        
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
    
       