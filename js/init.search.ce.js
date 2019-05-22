var colsDef={
    pratica:[[
        {title:'',field:'pratica',sortable:false,width:50,formatter: function(value,row,index){return '<a target="new" href="praticaweb.php?comm=1&pratica=' + value + '"><div class="ui-icon ui-icon-search"/></a>'}},
        {title:'Tipo Commissione',field:'tipo_commissione',sortable:true,width:300},
        {title:'Data Commissione',sortable:true,field:'data_convocazione',width:200},
        {title:'Sede',sortable:true,field:'sede1',width:3000},
    ]],
    delete:[[
        {title:'',field:'pratica',sortable:false,width:40,formatter: function(value,row,index){return '<input type="radio" data-testo="' + row['data_convocazione'] + '" name="pratica" id="' + value + '"class="textbox delete-radio"/>'}},
        {title:'Tipo Commissione',field:'tipo_commissione',sortable:true,width:150},
        {title:'Data Commissione',sortable:true,field:'data_convocazione',width:100},
        {title:'Sede',sortable:true,field:'sede1',width:100},
    ]],
    default_cols:[[
        {title:'',sortable:true,field:'',width:100},
    ]]

}