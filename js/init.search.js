var colsDef={
    civici:[[
        {title:'Indirizzo',field:'indirizzo',sortable:true,width:1000},
        //{title:'Via',field:'via',sortable:true,width:500},
        //{title:'Civico',field:'civico',sortable:true,width:100}
    ]],
    scadenze:[[
        {title:'Data Scadenza',field:'scadenza',sortable:true,width:200},
        {title:'GG. alla Scadenza',field:'diff',sortable:true,width:100},
        {title:'Scadenza',field:'testo',sortable:true,width:400},
        {title:'',field:'pratica',sortable:false,width:300,formatter: function(value,row,index){return '<a target="new" href="praticaweb.php?pratica=' + value + '">' + row['tipo_pratica'] + ' n° ' + row['numero'] + ' del ' + row['data_presentazione'] + '</a>'}},
        {title:'',hidden:true,field:'tipo_pratica',sortable:true,width:150},
        {title:'',hidden:true,field:'numero',sortable:true,width:100}
    ]],
    pratica:[[
        {title:'',field:'pratica',sortable:false,width:20,formatter: function(value,row,index){return '<a target="new" href="praticaweb.php?pratica=' + value + '"><div class="ui-icon ui-icon-search"/></a>'}},
        {title:'Tipo Pratica',field:'tipo_pratica',sortable:true,width:150},
        {title:'Numero',field:'numero',sortable:true,width:100},
        {title:'Protocollo',sortable:true,field:'protocollo',width:100},
        {title:'Data Prot.',sortable:true,field:'data_prot',width:100},
        
        {title:'Intervento',sortable:true,field:'tipo_intervento',width:150},
        {title:'Oggetto',sortable:true,field:'oggetto',width:350}
    ]],
    default_cols:[[
        {title:'',sortable:true,field:'',width:100},
    ]]

}