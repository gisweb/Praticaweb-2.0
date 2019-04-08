


_menuCloseDelay=500           // The time delay for menus to remain visible on mouse out
_menuOpenDelay=150            // The time delay before menus open on mouse over
_followSpeed=5                // Follow scrolling speed
_followRate=50                // Follow scrolling Rate
_subOffsetTop=-1               // Sub menu top offset
_subOffsetLeft=1              // Sub menu left offset
_scrollAmount=3               // Only needed for Netscape 4.x
_scrollDelay=20               // Only needed for Netcsape 4.x



with(menuStyle=new mm_style()){
itemwidth="160";
itemheight="19";
onbgcolor="#EEEDC0";
oncolor="#324362";
offbgcolor="#C2D0EA";
offcolor="#324362";
bordercolor="#415578";
borderstyle="solid";
borderwidth=1;
separatorcolor="#FFFFFF";
separatorsize="1";
padding=5;
fontsize="11px";
fontstyle="normal";
fontfamily="Verdana, Tahoma, Arial";
pagecolor="black";
pagebgcolor="#82B6D7";
headercolor="#000000";
headerbgcolor="#ffffff";
subimage="/pg_resources/code/arrow.gif";
subimagepadding="2";
}




// *** ANNOUNCEMENTS SUBMENU ******************************
with(milonic=new menuname("announcements")){
style=menuStyle;
itemwidth="220";
top=130;
left=162;
aI("text=Announcements listing;url=http://its.med.yale.edu/announcements/;");
aI("text=YSMInfo (YSM internal home page);url=http://info.med.yale.edu/ysm/ysminfo/;");
aI("text=Computing services page;url=http://its.med.yale.edu/computing_services.html;");
}
// ********************************************************


// *** GETTING STARTED SUBMENU ****************************
with(milonic=new menuname("getting_started")){
style=menuStyle;
itemwidth="220";
top=154;
left=162;
aI("text=Getting Started page;url=http://its.med.yale.edu/getting_started/index.html;");
aI("text=Buying computers;url=http://its.med.yale.edu/hardware/purchasing.html;");
aI("text=Getting software;showmenu=software_options;url=http://its.med.yale.edu/software/index.html");
aI("text=Network options;url=http://its.med.yale.edu/getting_started/detailedservices.html;");
aI("text=Remote access to Yale;url=http://its.med.yale.edu/software/remoteaccess/index.html;");
aI("text=Getting help;url=http://its.med.yale.edu/help/index.html;");
}
// ********************************************************


// *** SOFTWARE PLATFORMS SUB-SUBMENU *********************
with(milonic=new menuname("software_options")){
style=menuStyle;
aI("text=Software overview;url=http://its.med.yale.edu/software/;");
aI("text=Macintosh;url=http://its.med.yale.edu/software/mac/index.html;");
aI("text=Windows;url=http://its.med.yale.edu/software/win/index.html;");
aI("text=Palm;url=http://its.med.yale.edu/software/pda/index.html;");
aI("text=Unix & Linux;url=http://its.med.yale.edu/software/unix/index.html;");
}
// ********************************************************


// *** EMAIL SUBMENU **************************************
with(milonic=new menuname("email")){
style=menuStyle;
itemwidth="220";
top=178;
left=162;
aI("text=Email page;url=http://its.med.yale.edu/email/index.html;");
aI("text=Core account info;url=http://its.med.yale.edu/;");
aI("text=Student accounts;url=http://its.med.yale.edu/about_itsmed/academic_computing/meded/welcome.html#Students;");
aI("text=Anti Spam tools;url=https://webmail.med.yale.edu/;");
aI("text=Email FAQs;url=http://its.med.yale.edu/email/Email_Top10.html;");
aI("text=Web access to email;url=http://webmail.med.yale.edu/;");
aI("text=File transfer facility;url=https://transfer.med.yale.edu/;");
aI("text=Yale alumni life email;url=http://www.alumniconnections.com/olc/membersonly/YALE/permemail/permemail.cgi;");
}
// ********************************************************


// *** SECURITY SUBMENU ***********************************
with(milonic=new menuname("security")){
style=menuStyle;
itemwidth="220";
top=202;
left=162;
aI("text=Security home;url=http://its.med.yale.edu/security/index.html;");
aI("text=Safe computing measures;url=http://its.med.yale.edu/security/goodmeasures/;");
aI("text=Alerts & announcements;url=http://its.med.yale.edu/security/Alerts.html;");
aI("text=Norton Anti-Virus (NAV);showmenu=norton_options;");
aI("text=Anti Spam tools;url=https://webmail.med.yale.edu/;");
aI("text=HIPAA security;url=http://hipaa.yale.edu/security/;");
aI("text=Contact ISO-Med;url=http://its.med.yale.edu/security/contact.html;");
}
// ********************************************************

// *** NORTON PLATFORMS SUB-SUBMENU ***********************
with(milonic=new menuname("norton_options")){
style=menuStyle;
aI("text=Macintosh NAV;url=http://its.med.yale.edu/software/mac/index.html;");
aI("text=Windows NAV;url=http://its.med.yale.edu/software/win/index.html;");
}
// ********************************************************


// *** NETWORK CONNECTIONS SUBMENU ************************
with(milonic=new menuname("network")){
style=menuStyle;
itemwidth="220";
top=226;
left=162;
aI("text=Network page;url=http://its.med.yale.edu/network/index.html;");
aI("text=Detailed services;url=http://its.med.yale.edu/getting_started/detailedservices.html;");
aI("text=Network security;url=http://its.med.yale.edu/security/;");
aI("text=Client accounts;url=http://its.med.yale.edu/about_itsmed/cats/systemssupport.html;");
aI("text=Web design services;url=http://its.med.yale.edu/wdd/;");
}
// ********************************************************


// *** REMOTE ACCESS SUBMENU ******************************
with(milonic=new menuname("remote_access")){
style=menuStyle;
itemwidth="220";
top=250;
left=162;
aI("text=Remote access page;url=http://its.med.yale.edu/software/remoteaccess/index.html;");
aI("text=PPP dial-in;url=http://its.med.yale.edu/getting_started/detailedservices.html#remote;");
aI("text=Web access to email;url=http://webmail.med.yale.edu/;");
aI("text=VPN services;url=http://its.med.yale.edu/software/remoteaccess/vpn/index.html;");
aI("text=Proxy server;url=http://www.med.yale.edu/library/proxy/;");
}
// ********************************************************

// *** FORMS SUBMENU **************************************
with(milonic=new menuname("forms")){
style=menuStyle;
itemwidth="220";
top=274;
left=162;
aI("text=Forms & services;url=http://its.med.yale.edu/forms/index.html;");
aI("text=Computing Request Form;url=http://its.med.yale.edu/forms/crf.html;");
}
// ********************************************************


// *** SOFTWARE SUBMENU ***********************************
with(milonic=new menuname("software")){
style=menuStyle;
itemwidth="220";
top=298;
left=162;
aI("text=Software overview;url=http://its.med.yale.edu/software/;");
aI("text=Macintosh;url=http://its.med.yale.edu/software/mac/index.html;");
aI("text=Windows;url=http://its.med.yale.edu/software/win/index.html;");
aI("text=Palm;url=http://its.med.yale.edu/software/pda/index.html;");
aI("text=Unix & Linux;url=http://its.med.yale.edu/software/unix/index.html;");
}
// ********************************************************


// *** HARDWARE SUBMENU ***********************************
with(milonic=new menuname("hardware")){
style=menuStyle;
itemwidth="220";
top=322;
left=162;
aI("text=Hardware page;url=http://its.med.yale.edu/hardware/index.html;");
aI("text=Recommendations;showmenu=recommendations;");
aI("text=Repairs;url=http://its.med.yale.edu/getting_started/detailedservices.html#hardware;");
aI("text=Purchasing & supplies;url=http://its.med.yale.edu/hardware/purchasing.html;");
}
// ********************************************************

// *** HARDWARE RECOMMENDATIONS SUB-SUBMENU ***************
with(milonic=new menuname("recommendations")){
style=menuStyle;
aI("text=Apple Macintosh;url=http://its.med.yale.edu/hardware/hardware.html#mac;");
aI("text=Windows;url=http://its.med.yale.edu/hardware/hardware.html#pc;");
aI("text=Laptops;url=http://its.med.yale.edu/hardware/laptop/index.html;");
aI("text=Palm;url=http://its.med.yale.edu/pda/palm_recs.html;");
aI("text=Unix;url=http://its.med.yale.edu/hardware/unix/index.html;");
aI("text=Printers;url=http://its.med.yale.edu/hardware/printers/index.html;");
aI("text=Scanners;url=http://its.med.yale.edu/hardware/scanner/index.html;");
aI("text=Projectors;url=http://its.med.yale.edu/hardware/projectors/index.html;");
aI("text=Modems;url=http://its.med.yale.edu/hardware/modems/index.html;");
aI("text=Storage hardware;url=http://its.med.yale.edu/hardware/storage/index.html;");
}
// ********************************************************



// *** TECHNOLOGY SERVICES SUBMENU ***********************************
with(milonic=new menuname("tech_services")){
style=menuStyle;
itemwidth="220";
top=346;
left=162;
aI("text=Technology services page;url=http://its.med.yale.edu/about_itsmed/admin_systems/infosys.html;");
aI("text=Research database services;url=http://its.med.yale.edu/about_itsmed/admin_systems/infosys.html;");
aI("text=Server hosting;url=http://its.med.yale.edu/about_itsmed/admin_systems/infosys.html;");
aI("text=Database design;url=http://its.med.yale.edu/about_itsmed/admin_systems/infosys.html;");
aI("text=Programming;url=http://its.med.yale.edu/about_itsmed/admin_systems/infosys.html;");
aI("text=Web development;url=http://its.med.yale.edu/wdd;");
aI("text=Digital media consulting;url=http://its.med.yale.edu/about_itsmed/admin_systems/infosys.html;");
}
// ********************************************************




// *** ABOUT ITS-MED SUBMENU ******************************
with(milonic=new menuname("about_itsmed")){
style=menuStyle;
itemwidth="220";
top=370;
left=162;
aI("text=About ITS-Med page;url=http://its.med.yale.edu/index.html;");
aI("text=Organization;url=http://its.med.yale.edu/about_itsmed/directors/index.html;");
aI("text=Service units;showmenu=units;");
aI("text=Policies & services;url=http://its.med.yale.edu/about_itsmed/policies/index.html;");
aI("text=Advisory groups;url=http://its.med.yale.edu/about_itsmed/advisory_groups/index.html;");
aI("text=Staff directory;url=http://its.med.yale.edu/about_itsmed/staff_directory.html;");
}
// ********************************************************


// *** UNITS SUB-SUBMENU **********************************
with(milonic=new menuname("units")){
style=menuStyle;
aI("text=Academic Computing;url=http://its.med.yale.edu/about_itsmed/academic_computing/index.html;");
aI("text=Administration;url=http://its.med.yale.edu/about_itsmed/admin_billing/index.html;");
aI("text=Information Systems;url=http://its.med.yale.edu/about_itsmed/admin_systems/index.html;");
aI("text=Communications and Technical Support;url=http://its.med.yale.edu/about_itsmed/cats/index.html;");
aI("text=Research & Communications;url=http://its.med.yale.edu/about_itsmed/research/index.html;");
aI("text=MedMedia Group;url=http://its.med.yale.edu/mediaservices/;");
aI("text=Tech Evaluation & Planning;url=http://its.med.yale.edu/about_itsmed/tech_evaluation/index.html;");
aI("text=Systems Engineering;url=http://its.med.yale.edu/about_itsmed/ses/index.html;");
aI("text=Information Security;url=http://its.med.yale.edu/security/index.html;");
aI("text=Web Design & Development;url=http://its.med.yale.edu/wdd/index.html;");
}
// ********************************************************


// *** STAFF DIRECTORY SUBMENU ****************************
with(milonic=new menuname("staff")){
style=menuStyle;
itemwidth="220";
top=394;
left=162;
aI("text=Directory page;url=http://its.med.yale.edu/about_itsmed/staff_directory.html;");
aI("text=Unit directories;showmenu=unit_directories;");
aI("text=Yale directory;url=http://info.med.yale.edu/ysm/directory.html;");
}
// ********************************************************


// *** UNITS SUB-SUBMENU **********************************
with(milonic=new menuname("unit_directories")){
style=menuStyle;
aI("text=Academic Computing;url=http://its.med.yale.edu/about_itsmed/staff_directory.html#academic;");
aI("text=Administration;url=http://its.med.yale.edu/about_itsmed/staff_directory.html#itsmed;");
aI("text=Information Systems;url=http://its.med.yale.edu/about_itsmed/staff_directory.html#admin;");
aI("text=Communications and Technical Support;url=http://its.med.yale.edu/about_itsmed/staff_directory.html#cats;");
aI("text=Research & Communications;url=http://its.med.yale.edu/about_itsmed/staff_directory.html#research;");
aI("text=MedMedia Group;url=http://its.med.yale.edu/about_itsmed/staff_directory.html#media;");
aI("text=Tech Evaluation & Planning;url=http://its.med.yale.edu/about_itsmed/staff_directory.html#techeval;");
aI("text=Systems Engineering;url=http://its.med.yale.edu/about_itsmed/staff_directory.html#sec;");
aI("text=Information Security;url=http://its.med.yale.edu/about_itsmed/staff_directory.html#sec;");
aI("text=Web Design & Development;url=http://its.med.yale.edu/about_itsmed/staff_directory.html#wdd;");
}
// ********************************************************



// *** TEMPLATE SUBMENU ***********************************
with(milonic=new menuname("template")){
style=menuStyle;
itemwidth="220";
top=100;
left=162;
aI("text=Item;url=URL;");
aI("text=Item;url=URL;");
aI("text=Item;url=URL;");
aI("text=Item;url=URL;");
aI("text=Item;url=URL;");
}
// ********************************************************







drawMenus();













