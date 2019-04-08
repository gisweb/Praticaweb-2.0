//var hide_empty_list=true; //uncomment this line to hide empty selection lists

var disable_empty_list=true; //uncomment this line to disable empty selection lists



addListGroup("demo", "cs-top");

addList("cs-top", "scegli primo select", "scegli primo select", "cs-sub-1");
addList("cs-sub-1", "residenziale", "residenziale", "cs-sub-2");
addOption("cs-sub-2", "rnuova costruzione", "rnuova costruzione");
addOption("cs-sub-2", "rristrutturazione", "rristrutturazione");

addList("cs-sub-1", "turistica", "turistica", "cs-sub-3");
addOption("cs-sub-3", "tnuova costruzione", "tnuova costruzione");
addOption("cs-sub-3", "tristrutturazione", "tristrutturazione");

addOption("cs-sub-1", "commerciale", "commerciale");

