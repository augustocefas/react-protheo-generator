<?php include "config.php";

      
   /* $nomeDoModulo = 'CoberturaDePara';

    $camposComplete = [
        "codCoberturaDe"=>[
            "type"=>"string",
            "inputType"=>"text",
            "required"=>true,
            "table"=>[ 'titulo'=>'Código  Cobertura De']
        ],
        "codCoberturaPara"=>[
            "type"=>"number",
            "inputType"=>"text",
            "required"=>true,
            "table"=>[ 'titulo'=>'Código Cobertura Para']
        ]
    ];

    $masterKey = 'codCoberturaDe'; /*
*/

/*

   $nomeDoModulo = 'TabuaIdade';
   $camposComplete = [
        "codigoTabua"=>[
            "type"=>"number",
            "inputType"=>"text",
            "required"=>true,
            "table"=>[ 'titulo'=>'Codigo da tabua']
        ],
        "idade"=>[
            "type"=>"number",
            "inputType"=>"text",
            "required"=>true,
            "table"=>[ 'titulo'=>'Idade']
        ],
        "valorTaxa"=>[
            "type"=>"string",
            "inputType"=>"text",
            "required"=>true,
            "table"=>[ 'titulo'=>'Valor da taxa']],
        "sexo"=>[
            "type"=>"string",
            "inputType"=>"text",
            "required"=>true,
            "table"=>[ 'titulo'=>'Sexo']]
    ];
    $masterKey = 'codCoberturaDe';


*/


    $nomeDoModulo = 'Ramo';
   $camposComplete = [
        "id"=>[
            "type"=>"string",
            "inputType"=>"int",
            "required"=>true,
            "table"=>[ 'titulo'=>'Codigo do ramo']
        ],
        "nomeRamo"=>[
            "type"=>"string",
            "inputType"=>"text",
            "required"=>true,
            "table"=>[ 'titulo'=>'Ramo de atividade']]
    ];
    $masterKey = 'id'; /*

   $camposComplete  = [
        "codigoTabua"=>[
            "type"=>"number", 
            "required"=>true, 
            "table"=>[ 'titulo'=>'Codigo']],
        "idade"=> ["type"=>"number", "required"=>true, "table"=>['titulo'=>'idade']],
        "sexo"=> ["type"=>"string", "required"=>true, "table"=>['titulo'=>'Sexo']],
        "idCobertura"=> ["type"=>"number", "required"=>true, "table"=>['titulo'=>'id Cobertura']],
        "dataInicioVigencia"=> ["type"=>"string", "required"=>true, "table"=>['titulo'=>'Data início vigência']],
        "dataFimVigencia"=> ["type"=>"string", "required"=>true, "table"=>['titulo'=>'Data fim vigência']],
        "dataCadastro"=>  ["type"=>"string", "required"=>true, "table"=>['titulo'=>'Data cadastro']],
        "idStatus"=> ["type"=>"number", "required"=>true, "table"=>['titulo'=>'idStatus']],
        "nomeStatus"=> ["type"=>"string", "required"=>true, "table"=>['titulo'=>'Status']],
    ];
  $masterKey = 'codCoberturaDe';
   */
  


    $updateKey = [];
    $deleteKey = [];
    
    use App\Generator\CrudGenerator;

    $crud = new CrudGenerator($nomeDoModulo, ROOT.DS.'output', $camposComplete);
        $crud -> setFields($camposComplete);
        $crud -> setMasterKey($masterKey);
        $crud -> setDeleteUpdateKey($camposComplete, $camposComplete, 'update');
        $crud -> setDeleteUpdateKey($camposComplete, [], 'delete');
        
        
        //gera extrutura de pastas-------------------
       $crud->createModuleFolders();
        
       //gera controller
       $crud->generateController();
       //gera model
       $crud->generateModel();

       //gera view do modulo
       $crud->generateEntryPage();

      //gera form
       $crud->generateForm();
       //gera table
       $crud->generateTable();
       //gera arquivo de rotas
       $crud->generateRootFiles();
    
       