var colsDef={
    pratica:[[
        {title:'',field:'pratica',sortable:false,width:20,formatter: function(value,row,index){return '<a target="new" href="praticaweb.php?storage=1&pratica=' + value + '"><div class="ui-icon ui-icon-search"/></a>'}},
        {title:'Protocollo',sortable:true,field:'protocollo',width:100},
        {title:'Data Prot.',sortable:true,field:'data_protocollo',width:100},
        {title:'Oggetto',sortable:true,field:'oggetto',width:350},
        {title:'Soggetto',sortable:true,field:'soggetto',width:350}
    ]],
    delete:[[
        {title:'',field:'pratica',sortable:false,width:40,formatter: function(value,row,index){return '<input type="radio" data-testo="' + row['numero'] + '" name="pratica" id="' + value + '"class="textbox delete-radio"/>'}},
        {title:'Numero',field:'numero',sortable:true,width:100},
        {title:'Protocollo',sortable:true,field:'protocollo',width:100},
        {title:'Data Prot.',sortable:true,field:'data_prot',width:100},
        {title:'Oggetto',sortable:true,field:'oggetto',width:350},
        {title:'Richiedenti',sortable:true,field:'richiedente',width:350}
    ]],
    default_cols:[[
        {title:'',sortable:true,field:'',width:100},
    ]]

}