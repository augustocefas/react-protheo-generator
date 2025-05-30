<?php include "config.php";

      
    $nomeDoModulo = 'DeParaCobertura';

    $campos = [
        'id' => 'number',
        'nome' => 'string',
        'descricao' => 'string',
    ];

    $camposComplete = [
        'id' => [
            'type' => 'number', 
            'required' => true, 
            'table'=>[]
        ]
        
    ];

    $masterKey = 'id';

    $updateKey = [];
    $deleteKey = [];
    
    use App\Generator\CrudGenerator;

    $crud = new CrudGenerator($nomeDoModulo, ROOT.DS.'output', $campos);
        $crud -> setFields($camposComplete);
        $crud -> setMasterKey($masterKey);
        $crud -> setDeleteUpdateKey($campos, $campos, 'update');
        $crud -> setDeleteUpdateKey($campos, [], 'delete');
        
        
        //gera extrutura de pastas-------------------
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
       //gera arquivos raiz
       $crud->generateRootFiles();
    
       