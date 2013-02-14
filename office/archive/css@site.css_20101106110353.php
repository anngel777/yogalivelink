/* div {  border : 1px dashed #f00;} */


/* =================BODY================= */

body {
    text-align : center;
    background-color : #D3D3D3;
    font-family : Verdana, Arial, Helvetica, sans-serif;
    background-image: url(/images/template/background_tile.jpg); 
    background-repeat : repeat;
}

#container {
  text-align : left;
  margin : 0px auto;
  width : 982px;
}



.container_gray {
    background-color:#F8F8F8;
    color:#000;
    padding:20px;
}

.container_dark {
    background-color:#212018;
    color:#fff;
    padding:10px;
}

.container_light {
    background-color:#4E4726;
    color:#fff;
    padding:10px;
}



.tinyBox {
    width:200px;
    height:100px;
    padding:5px;
    border:1px solid #ddd;
}
.tinyBoxFill {
    background-color:gray;
    height:100%;
}


.col {
    float:left;
}
.clear {
    clear:both;
}


#blank_body {
  background-color : #fff;
  text-align : left;
  font-size: 0.8em; 
}








/* =================FOOTER================= */
#footertext {
  text-align:left; 
  color:#cccccc; 
  font-size:10px; 
  padding-left:20px; 
  padding-right:20px;
}



/* =================MENU================= */

#menu_holder {
    height: 40px;
    background-color:#261918;
} 
/*
#nav {
    height: 58px;
    width: 542px;
    margin: 0px auto;
    padding: 0px;
    background-image: url(right.png);
    background-repeat: no-repeat;
    background-position: right;
}
*/
#nav ul {
    margin: 0px;
    padding: 0px 0px 0px 21px;
    list-style-type: none;
}
#nav li{
    margin: 0px;
    padding: 0px;
    float: left;
    height: 40px;
}
#nav li a:link, #nav li a:active, #nav li a:visited {
    /*background-image: url(middle.png);*/
    background-color:#261918;
    background-repeat: repeat-x;
    /*height: 58px;*/
    width: 100px;
    display: block;
    /*line-height: 58px;*/
    font-weight: bold;
    color: #666666;
    text-decoration: none;
    font-family: Arial, Helvetica, sans-serif;
    text-align: center;
    margin-top:10px;
    /*font-size: 120%;*/
}
#nav li a:focus, #nav li a:hover{
    text-decoration : none;
    /*-moz-outline:0;*/
    color:#fff;
    background-color: #350610;
    /*background-image: url(rollOver.png);*/
    background-repeat: repeat-x;
}







/* ================= ARTICLE ================= */


.article_title {
    font-size:18px;
    font-weight:bold;
    color:#9C1E21;
}
.article_author {
    font-size:14px;
    font-weight:normal;
    font-style:italic; 
    color:#E49433;
    border-bottom:1px solid #999;
}
.article_source {
    font-size:10px;
    font-weight:normal;
    font-style:italic; 
    color:#999;
}
.article_p {
    font-size:12px;
    padding:10px 20px 0px 20px;
    text-align:justify;
}
.article_p:first-letter {
    font-size: 14px;
    font-weight:bold;
    /*font-size: 3em;*/
    /*font-family: "Edwardian Script ITC", "Brush Script MT", cursive;*/
}
.article_footer {
    font-size:8px;
    font-style:italic; 
}
































/* =================CONTENT HEADER================= */
#content_top div {
}

#content_top_top {
  background-image: url(/x/images/template_master/content_top_top.jpg); 
  background-repeat : no-repeat;
  width : 848px;
  height : 12px; 
}

/* --IE 6 HACK-- */
/*\*/ * html #content_top_top {margin-bottom : -6px;} /**/

 

#content_top_left {
  background-image: url(/x/images/template_master/content_top_left.jpg); 
  background-repeat : no-repeat;
  width : 23px;
  height : 20px;
  float : left;
}

#content_top_image {
  width : 808px;
  height : 20px;
  float : left;
  background-color : #00f;
}

#content_top_right {
  background-image: url(/x/images/template_master/content_top_right.jpg); 
  background-repeat : no-repeat;
  width : 17px;
  height : 20px;
  float : left;
}

#content_top_bottom {
  background-image: url(/x/images/template_master/content_top_bottom.jpg); 
  background-repeat : no-repeat;
  width : 848px;
  height : 6px;
  clear : both;
}




/* =================CONTENT FOOTER================= */
#content_bottom div {
}

#content_bottom_top {
  background-image: url(/x/images/template_master/content_bottom_top.jpg); 
  background-repeat : no-repeat;
  width : 848px;
  height : 24px; 
}

/* --IE 6 HACK-- */
/*\*/ * html #content_bottom_top {margin-bottom : -6px;} /**/

 

#content_bottom_left {
  background-image: url(/x/images/template_master/content_bottom_left.jpg); 
  background-repeat : no-repeat;
  width : 23px;
  height : 25px;
  float : left;
}

#content_bottom_image {
  width : 808px;
  height : 25px;
  float : left;
  background-color : #00f;
}

#content_bottom_right {
  background-image: url(/x/images/template_master/content_bottom_right.jpg); 
  background-repeat : no-repeat;
  width : 17px;
  height : 25px;
  float : left;
}

#content_bottom_bottom {
  background-image: url(/x/images/template_master/content_bottom_bottom.jpg); 
  background-repeat : no-repeat;
  width : 848px;
  height : 17px;
  clear : both;
}





/* =================CONTENT================= */

#___content_top {
  background-image: url(/x/images/template_master/body_top.jpg);
  width : 848px;
  height : 37px;
}

#content_container {
  background-image: url(/x/images/template_master/body_middle.jpg);
  width : 848px;
}


#content {
  text-align:left; 
  font-size: 0.8em; 
  /*width : 780px;*/
  margin-left: 16px;
  margin-right: 10px;
  /*border : 1px solid #990000;*/
}

#body_bottom {
  background-image: url(/x/images/template_master/body_bottom.jpg);
  width : 848px;
  height : 66px;
}








/* =================CONTACT UPDATE PROFILE================= */

#overlay {
  position : absolute;
  background-color : #000;
  top : 0px;
  left : 0px;
  width : 100%;
  z-index:100; 
  opacity : 0.8;
}

#EDIT_CONTACT_PROFILE {
  position: absolute;
  z-index : 1000;
  width : 700px;
  left : 50%;
  top : 95px;
  margin-left: -350px;
  background-color : #ddd;
  color : #000;
  padding : 0px;
  border : 1px solid #fff;
  display : none;
}

#EDIT_CONTACT_PROFILE_CONTAINER {
  padding : 0px 25px 15px 25px;
}

#EDIT_CONTACT_PROFILE h1 {
  
}

#CLOSE_EDIT_CONTACT_PROFILE {
  display : block;
  border-left   : 1px solid #fff;
  border-bottom : 1px solid #fff;
  text-decoration : none;
  color : #fff;
  width : 1em;
  text-align : center;
  font-weight : bold;
  float : right;
  background-color : #006;
}
#CLOSE_EDIT_CONTACT_PROFILE:hover {
  background-color : #fff;
  color : #000;
}


/* =================BUTTONS================= */
a.stdbutton, a.stdbuttoni {
  font-size : 80%;
  text-decoration : none;
  display : block;
  border : 1px solid #888;
  background-color : #ccc;
  color : #000;
  margin : 0.25em 0em;
  padding : 0.25em;
  text-align : center;
}

a.stdbutton {
  display : block;
}

a.stdbuttoni {
  display : inline;
}

a.stdbutton:active , a.stdbuttoni:active {
  border-color : #345 #cde #def #678;
}

a.stdbutton:hover , a.stdbuttoni:hover {
  background-color : #eee;
  color : #000;
}


/* =================PRINT VERSION================= */
body.print {
  background-color : #fff;
  text-align : left;
}

#pheader {
  text-align : right;
  border-bottom : 2px solid #006;
}

#pheader h1 {
  color : #fff;
  font-size : 2em;
  margin : 0px 40px;
}

#return {
  position : absolute;
  font-size : 0.9em;
  top : 0px;
}

#return .stdbutton {
  display : inline;
}

#pcontent {
  padding : 1em;
}

.printfooter {
  text-align: left; 
  font-color:#cccccc;
  font-size:10px; 
  padding-left:20px; 
  padding-right:20px;
}


/* =================FORM ELEMENTS================= */
.formitem {
  background-color : #eee;
  border : 1px solid #000;
}
.formitem_checkbox {
  background-color : transparent;
  border-width : 0px;
}

.formitemerror {
  background-color : #ff7;
  border : 1px solid #000;
}

span.formrequired {
  color : #f00;
  font-weight : bold;
  padding-right : 2px;
}

.formtitlebreak{
  line-height:1px;
  clear: both;
}

div.formtitle {
  float : left;
  width : 160px;
  font-weight : bold;
  padding : 3px 0px;
  font-size : 0.8em;
  text-align : right;
}

div.forminfo {
  padding : 3px 0px;
  margin-left : 170px;
  font-size : 0.8em;
}

input.formsubmit {
  color : #000;
  cursor : pointer;
  font-size : 1em;
}

div.error {
  margin : 10px auto;
  border : 2px solid #f00;
  background-color : #f88;
  padding : 0.5em;
  width : 300px;
  text-align : center;
}

.form_comment_text {
  margin:0px 3em 1em 2em;
}

/* =================GENERAL ELEMENTS================= */

.center {
  text-align : center;
}

a {
  color : #036;
}

a:hover {
  color : #036;
  background-color : #ccc;
}

h1 {
  color : #006;
  font-family : Arial, Helvetica, sans-serif;
  font-size : 1.4em;
}

h2 {
  color : #036;
  font-size : 1.2em;  
}

h3 {
  color : #f00;
  font-size : 1.1em; 
}

/* =================IMAGES================= */
img.left {
  float : left;
  margin-right : 1em;
  vertical-align : text-top;
}

img.right {
  float : right;
  margin-left : 1em;
  vertical-align : text-top;
}

a.imagelink {
  padding : 3px;
  display : block;
}

a.imagelink:hover {
  color : #ccc
}

/* =================MISC ITEMS================= */
p.legalnotice {
  text-align : center;
  font-size : 0.8em;
  font-weight : bold;
}

p.copyright {
  text-align : center;
  font-size : 0.8em;
  color : #253161;
  line-height : 1.5em;
}

a.mvp {
  color : #fff;
  text-decoration : none;
}

a.mvp:hover {
  color : #000;
}

.center {
  text-align : center;
}

a.printversion {
  width : 80px;
  height : 20px;
  background : url(/images/printversion.gif) no-repeat;
  display : block;
  margin-right : 15px;
  border : 2px solid transparent;
  float : right;
}

a.printversion:hover {
  background-color : #888;
}

a.validator {
  width : 60px;
  height : 21px;
  background : url(/images/valid-xhtml10.png) no-repeat;
  display : block;
  border : 2px solid transparent;
  margin : 2em auto;
}

a.validator:hover {
  background-color : #888;
}

#error {
  background-color : #f66;
  padding : 1em;
  border : 2px solid #f00;
  text-align : center;
  margin : 1em;
  font-weight : bold;
}

#message {
  background-color : #fff;
  padding : 1em;
  border : 2px solid #036;
  text-align : center;
  margin : 1em;
  font-weight : bold;
  color : #036;
}

#flash {
  position : absolute;
  top : 100px;
  left : 50%;
  margin-left : -250px;
  width : 500px;
  background-color : #ff7;
  border : 2px solid #888;
  color : #000;
  padding : 10px;
  text-align : center;
  z-index : 10000;
}

/* ============================== AUTOCOMPLETE ================================ */

.ac_input {
  border : 1px solid #050;
  background : url(/wo/images/tiny_arrow.gif) right center no-repeat #D5EFD1;
}

.ac_results {
  padding : 0px;
  border : 1px solid #050;
  background-color : #F0FFEF;
  overflow : hidden;
}

.ac_results ul {
  width: 100%;
  list-style-position : outside;
  list-style : none;
  padding : 0;
  margin : 0;
}

.ac_results li {
  margin : 0px;
  padding : 2px 5px;
  cursor : default;
  display : block;
  width: 100%;
  font : menu;
  font-size : 12px;
  overflow : hidden;
  border-bottom : 1px dotted #888;
}

.ac_loading {
  background : url(/wo/images/indicator.gif) right center no-repeat;
}

.ac_over {
  background-color : Highlight;
  color : HighlightText;
}

.popup {
  position : absolute;
  left : 0px;
  top : 0px;
  width: 132px;
  border-style : solid;
  border-width: 4px;
  border-color : blue;
  background-color : yellow;
  padding : 5px;
  color : red;
  font-family : Arial;
  font-weight : bold;
  font-size : 10pt;
  z-index : 2;
  visibility : hidden;
}


.form_box {
  border:1px dashed #bbbbbb;
  padding:10px;
  width:90%;
  background-color:#dddddd;
}

#registered_box {
  text-align:center; 
  font-weight:bold; 
  color:blue; 
  margin-bottom: 2em;
}
#waitlisted_box {
  text-align:center; 
  font-weight:bold; 
  background-color:#a22c22; 
  color:#ffffff; margin-bottom: 2em;
}


<!-- ==================== CONTACT INFO ==================== -->

#contact_record {
   width : 720px;
   background-color : #ddd;   
}

#contact_record td, #contact_record th {
  padding : 3px;
  font-size : 0.8em;
}
#contact_record th {
  width : 200px;
  background-color : #ddd; 
}
#contact_record td {
  width : 510px;
  background-color : #eee; 
}

#contact_profile_update_button {
  display : block;
  float : right;
  text-decoration : none;
  background-color : #888;
  font-size : 1.2em;
  padding : 3px 4px;
  border : 1px solid #fff;
  margin : 5px;
  color : #fff;
}
#contact_profile_update_button:hover {
  background-color : #006;
}

/* =========================TABS=========================== */

a.tablink,a.tabselect{
  border-left:1px solid #888;
  border-right:1px solid #888;
  border-top:1px solid #888;
  color:#000;

  float:left;
  padding:2px 1em;
  text-decoration:none;
  font-size:1em;
  margin:0px;
}

a.tabselect:hover, a.tablink:hover{
  background-color:#888;
  color:#fff;
  border-bottom:1px solid #888;
}

div.tabspacer{
  background-color:#fff;
  padding:2px 1em;
  text-decoration:none;
  font-size:1em;
  margin:0px;
  border-top:1px solid #fff;
  border-bottom:1px solid #888;
}

a.tabselect {
  border-bottom:1px solid #fff;
  background-color: #fff;
}

a.tablink{
  background-color:#E5D8DF;
  border-bottom:1px solid #888;
}

div.tabfolder{
  border-left:1px solid #888;
  border-right:1px solid #888;
  border-bottom:1px solid #888;
  padding:10px;
}

.event_info {
  font-size: 11px;
  padding-left: 20px;
}
.event_header {
  width: 200px;
  font-weight: bold;
  font-size:14px;
  color: #24486C;
  padding: 3px;
  text-align: right;
  background-color: #eeeeee;
}

/* ========================= INDEX GEO =========================== */
.geo_header {
    font-weight: bold;
    font-size:12px;
    color: #24486C;
    padding-bottom: 5px;
    border-bottom:1px solid #cccccc;
}

.geo_event_wrap {
    width: 100%;
}

.geo_event {
  padding-left: 20px;
  font-size: 11px;
  width: 100%;
}
.geo_language {
  width: 100px;
}




.geo_date {
  color: #6699CC;
}
.geo_location {
  color: #000000;
}
.geo_country {
  color: #666666;
}
.section_break {
  height: 20px;
}

.geo_city {
  color: #000000;
}

.geo_header_date {
  font-weight: bold;
  font-size:12px;
  color: #24486C;
  padding-bottom: 5px;
  text-align: right;
}

.agenda_time {
  color: #6699CC;
  width: 150px;
  padding-right:10px;
}
.agenda_description {
  color: #000000;
  width: 400px;
  font-size: 11px;
}
.event_agenda_table {
  width: 500px;
  padding-left: 20px;
}



/* =========================INDEX=========================== */
#EVENT_LIST_HEADING {
  margin-top : 2em;
  font-size:16px; 
  font-weight:bold; 
  background-color:#e2e2e2; 
  color:#a22c22; 
  padding:5px;
}

#event_listing {
  padding-left:30px; 
  padding-right:30px; 
  padding-top:30px;
}

#REGISTRATION_SUPPORT_BOX {
  width:250px; 
  font-face:verdana;
  float : right;
  background-color:#dddddd; 
  padding:3px;
  margin: 1em 0px 1em 2em;

}



#REGISTRATION_SUPPORT_BOX_1 {
  height:20px; 
  background-color:#0F1E3D;
  color:#FFFFFF; 
  padding:2px; 
  padding-right:5px; 
  text-align:right; 
  border-bottom:3px solid #dddddd;
}

#REGISTRATION_SUPPORT_BOX_2 {
  background-color:#dddddd;
}

#REGISTRATION_SUPPORT_BOX_3 {
  height:60px; 
  background-color:#0A5284; 
}

#REGISTRATION_SUPPORT_BOX_4 {
  padding-top : 10px;
  color:#ffffff;
  font-size:18px; 
  text-align:right;
  padding-right:30px; 
  font-weight:normal;"
}
#REGISTRATION_SUPPORT_BOX_5 {
  color:#000000;
  font-size:12px;
  text-align:right;
  padding-right:30px;
}

.registration_notice {
  padding:5px; background-color:#990000; color:#fff; font-weight:bold;
}

/* ========================= REGISTRATION STEPS =========================== */

.stepwrap {
  float: left;
  text-align: center;
  font-size: 10px;
  padding: 5px; 
}
.stepbox {
  border: 1px solid #ddd;
  padding: 3px;
  width: 90px; 
} 

.stepbox_off {
  background-color: #fff;
  color: #000000;
  font-size: 9px; 
}

.stepbox_on {
  /* background-color: #990000; */
  background-color: #0f0;
  background-image: url(/images/semi-transparent.gif);

  color: #990000;
  font-size: 9px; 
}

.stepbox_completed {
  background-color: #888; 
  background-image: url(/images/semi-transparent.gif);
  font-size: 9px;
}

.cancel_link {
   text-transform:none;
   background:url(/images/unchecked.gif) no-repeat 0 2px;
   padding-left : 17px;   
}


.register_link {
   text-transform:none;
   background:url(/images/checked.gif) no-repeat 0 2px;
   padding-left : 17px;   
}

/* --------------- SPONSOR -------------- */
#sponsor_name {
  float : right;
  margin-right : 1em;
  padding : 5px;
  border : 1px dotted #888;
}

a.registernow {
   padding:60px 16px;
   margin:0 0 0 26px;
   position:relative;
   top:112px;
   background:url(../images/event_unique/90_index_registernow.jpg)no-repeat left;
   text-decoration:none;
}

a.registernow:hover {
   background-position:right;
   margin:0 0 0 30px;
}

a.registernow span {
   position:fixed;
   left:-1000px;
}
a.attendnow {
   padding:60px 16px;
   margin:0 0 0 26px;
   position:relative;
   top:112px;
   background:url(../images/event_unique/90_index_attendnow.jpg)no-repeat left;
   text-decoration:none;
}

a.attendnow:hover {
   background-position:right;
   margin:0 0 0 30px;
}

a.attendnow span {
   position:fixed;
   left:-1000px;
}


t-transform:none;
   background:url(/images/checked.gif) no-repeat 0 2px;
   padding-left : 17px;   
}

/* --------------- SPONSOR -------------- */
#sponsor_name {
  float : right;
  margin-right : 1em;
  padding : 5px;
  border : 1px dotted #888;
}

a.registernow {
   padding:60px 16px;
   margin:0 0 0 26px;
   position:relative;
   top:112px;
   background:url(../images/event_unique/90_index_registernow.jpg)no-repeat left;
   text-decoration:none;
}

a.registernow:hover {
   background-position:right;
   margin:0 0 0 30px;
}

a.registernow span {
   position:fixed;
   left:-1000px;
}
a.attendnow {
   padding:60px 16px;
   margin:0 0 0 26px;
   position:relative;
   top:112px;
   background:url(../images/event_unique/90_index_attendnow.jpg)no-repeat left;
   text-decoration:none;
}

a.attendnow:hover {
   background-position:right;
   margin:0 0 0 30px;
}

a.attendnow span {
   position:fixed;
   left:-1000px;
}


/* div {  border : 1px dashed #f00;} */


/* =================BODY================= */

body {
  text-align : center;
  background-color : #D3D3D3;
  font-family : Verdana, Arial, Helvetica, sans-serif;
}

#blank_body {
  background-color : #fff;
  text-align : left;
  font-size: 0.8em; 
}

#container {
  text-align : left;
  margin : 0px auto;
  width : 848px;
}

/* =================HEADER================= */
#header div {
}

#header_top {
  background-image: url(/x/images/template_master/header_top.jpg); 
  background-repeat : no-repeat;
  width : 848px;
  height : 12px; 
}

/* --IE 6 HACK-- */
/*\*/ * html #header_top {margin-bottom : -6px;} /**/

 

#header_left {
  background-image: url(/x/images/template_master/header_left.jpg); 
  background-repeat : no-repeat;
  width : 23px;
  height : 165px;
  float : left;
}

#header_image {
  width : 808px;
  height : 165px;
  float : left;
  background-color : #00f;
}

#header_right {
  background-image: url(/x/images/template_master/header_right.jpg); 
  background-repeat : no-repeat;
  width : 17px;
  height : 165px;
  float : left;
}

#header_bottom {
  background-image: url(/x/images/template_master/header_bottom.jpg); 
  background-repeat : no-repeat;
  width : 848px;
  height : 7px;
  clear : both;
}





/* =================CONTENT HEADER================= */
#content_top div {
}

#content_top_top {
  background-image: url(/x/images/template_master/content_top_top.jpg); 
  background-repeat : no-repeat;
  width : 848px;
  height : 12px; 
}

/* --IE 6 HACK-- */
/*\*/ * html #content_top_top {margin-bottom : -6px;} /**/

 

#content_top_left {
  background-image: url(/x/images/template_master/content_top_left.jpg); 
  background-repeat : no-repeat;
  width : 23px;
  height : 20px;
  float : left;
}

#content_top_image {
  width : 808px;
  height : 20px;
  float : left;
  background-color : #00f;
}

#content_top_right {
  background-image: url(/x/images/template_master/content_top_right.jpg); 
  background-repeat : no-repeat;
  width : 17px;
  height : 20px;
  float : left;
}

#content_top_bottom {
  background-image: url(/x/images/template_master/content_top_bottom.jpg); 
  background-repeat : no-repeat;
  width : 848px;
  height : 6px;
  clear : both;
}




/* =================CONTENT FOOTER================= */
#content_bottom div {
}

#content_bottom_top {
  background-image: url(/x/images/template_master/content_bottom_top.jpg); 
  background-repeat : no-repeat;
  width : 848px;
  height : 24px; 
}

/* --IE 6 HACK-- */
/*\*/ * html #content_bottom_top {margin-bottom : -6px;} /**/

 

#content_bottom_left {
  background-image: url(/x/images/template_master/content_bottom_left.jpg); 
  background-repeat : no-repeat;
  width : 23px;
  height : 25px;
  float : left;
}

#content_bottom_image {
  width : 808px;
  height : 25px;
  float : left;
  background-color : #00f;
}

#content_bottom_right {
  background-image: url(/x/images/template_master/content_bottom_right.jpg); 
  background-repeat : no-repeat;
  width : 17px;
  height : 25px;
  float : left;
}

#content_bottom_bottom {
  background-image: url(/x/images/template_master/content_bottom_bottom.jpg); 
  background-repeat : no-repeat;
  width : 848px;
  height : 17px;
  clear : both;
}





/* =================CONTENT================= */

#___content_top {
  background-image: url(/x/images/template_master/body_top.jpg);
  width : 848px;
  height : 37px;
}

#content_container {
  background-image: url(/x/images/template_master/body_middle.jpg);
  width : 848px;
}


#content {
  text-align:left; 
  font-size: 0.8em; 
  /*width : 780px;*/
  margin-left: 16px;
  margin-right: 10px;
  /*border : 1px solid #990000;*/
}

#body_bottom {
  background-image: url(/x/images/template_master/body_bottom.jpg);
  width : 848px;
  height : 66px;
}





/* =================FOOTER================= */
#footer {
}

#footertext {  
  text-align: left; 
  font-color:#cccccc; 
  font-size:10px; 
  padding-left:20px; 
  padding-right:20px;
}


/* =================CONTACT UPDATE PROFILE================= */

#overlay {
  position : absolute;
  background-color : #000;
  top : 0px;
  left : 0px;
  width : 100%;
  z-index:100; 
  opacity : 0.8;
}

#EDIT_CONTACT_PROFILE {
  position: absolute;
  z-index : 1000;
  width : 700px;
  left : 50%;
  top : 95px;
  margin-left: -350px;
  background-color : #ddd;
  color : #000;
  padding : 0px;
  border : 1px solid #fff;
  display : none;
}

#EDIT_CONTACT_PROFILE_CONTAINER {
  padding : 0px 25px 15px 25px;
}

#EDIT_CONTACT_PROFILE h1 {
  
}

#CLOSE_EDIT_CONTACT_PROFILE {
  display : block;
  border-left   : 1px solid #fff;
  border-bottom : 1px solid #fff;
  text-decoration : none;
  color : #fff;
  width : 1em;
  text-align : center;
  font-weight : bold;
  float : right;
  background-color : #006;
}
#CLOSE_EDIT_CONTACT_PROFILE:hover {
  background-color : #fff;
  color : #000;
}


/* =================BUTTONS================= */
a.stdbutton, a.stdbuttoni {
  font-size : 80%;
  text-decoration : none;
  display : block;
  border : 1px solid #888;
  background-color : #ccc;
  color : #000;
  margin : 0.25em 0em;
  padding : 0.25em;
  text-align : center;
}

a.stdbutton {
  display : block;
}

a.stdbuttoni {
  display : inline;
}

a.stdbutton:active , a.stdbuttoni:active {
  border-color : #345 #cde #def #678;
}

a.stdbutton:hover , a.stdbuttoni:hover {
  background-color : #eee;
  color : #000;
}


/* =================PRINT VERSION================= */
body.print {
  background-color : #fff;
  text-align : left;
}

#pheader {
  text-align : right;
  border-bottom : 2px solid #006;
}

#pheader h1 {
  color : #fff;
  font-size : 2em;
  margin : 0px 40px;
}

#return {
  position : absolute;
  font-size : 0.9em;
  top : 0px;
}

#return .stdbutton {
  display : inline;
}

#pcontent {
  padding : 1em;
}

.printfooter {
  text-align: left; 
  font-color:#cccccc;
  font-size:10px; 
  padding-left:20px; 
  padding-right:20px;
}


/* =================FORM ELEMENTS================= */
.formitem {
  background-color : #eee;
  border : 1px solid #000;
}
.formitem_checkbox {
  background-color : transparent;
  border-width : 0px;
}

.formitemerror {
  background-color : #ff7;
  border : 1px solid #000;
}

span.formrequired {
  color : #f00;
  font-weight : bold;
  padding-right : 2px;
}

.formtitlebreak{
  line-height:1px;
  clear: both;
}

div.formtitle {
  float : left;
  width : 160px;
  font-weight : bold;
  padding : 3px 0px;
  font-size : 0.8em;
  text-align : right;
}

div.forminfo {
  padding : 3px 0px;
  margin-left : 170px;
  font-size : 0.8em;
}

input.formsubmit {
  color : #000;
  cursor : pointer;
  font-size : 1em;
}

div.error {
  margin : 10px auto;
  border : 2px solid #f00;
  background-color : #f88;
  padding : 0.5em;
  width : 300px;
  text-align : center;
}

.form_comment_text {
  margin:0px 3em 1em 2em;
}

/* =================GENERAL ELEMENTS================= */

.center {
  text-align : center;
}

a {
  color : #036;
}

a:hover {
  color : #036;
  background-color : #ccc;
}

h1 {
  color : #006;
  font-family : Arial, Helvetica, sans-serif;
  font-size : 1.4em;
}

h2 {
  color : #036;
  font-size : 1.2em;  
}

h3 {
  color : #f00;
  font-size : 1.1em; 
}

/* =================IMAGES================= */
img.left {
  float : left;
  margin-right : 1em;
  vertical-align : text-top;
}

img.right {
  float : right;
  margin-left : 1em;
  vertical-align : text-top;
}

a.imagelink {
  padding : 3px;
  display : block;
}

a.imagelink:hover {
  color : #ccc
}

/* =================MISC ITEMS================= */
p.legalnotice {
  text-align : center;
  font-size : 0.8em;
  font-weight : bold;
}

p.copyright {
  text-align : center;
  font-size : 0.8em;
  color : #253161;
  line-height : 1.5em;
}

a.mvp {
  color : #fff;
  text-decoration : none;
}

a.mvp:hover {
  color : #000;
}

.center {
  text-align : center;
}

a.printversion {
  width : 80px;
  height : 20px;
  background : url(/images/printversion.gif) no-repeat;
  display : block;
  margin-right : 15px;
  border : 2px solid transparent;
  float : right;
}

a.printversion:hover {
  background-color : #888;
}

a.validator {
  width : 60px;
  height : 21px;
  background : url(/images/valid-xhtml10.png) no-repeat;
  display : block;
  border : 2px solid transparent;
  margin : 2em auto;
}

a.validator:hover {
  background-color : #888;
}

#error {
  background-color : #f66;
  padding : 1em;
  border : 2px solid #f00;
  text-align : center;
  margin : 1em;
  font-weight : bold;
}

#message {
  background-color : #fff;
  padding : 1em;
  border : 2px solid #036;
  text-align : center;
  margin : 1em;
  font-weight : bold;
  color : #036;
}

#flash {
  position : absolute;
  top : 100px;
  left : 50%;
  margin-left : -250px;
  width : 500px;
  background-color : #ff7;
  border : 2px solid #888;
  color : #000;
  padding : 10px;
  text-align : center;
  z-index : 10000;
}

/* ============================== AUTOCOMPLETE ================================ */

.ac_input {
  border : 1px solid #050;
  background : url(/wo/images/tiny_arrow.gif) right center no-repeat #D5EFD1;
}

.ac_results {
  padding : 0px;
  border : 1px solid #050;
  background-color : #F0FFEF;
  overflow : hidden;
}

.ac_results ul {
  width: 100%;
  list-style-position : outside;
  list-style : none;
  padding : 0;
  margin : 0;
}

.ac_results li {
  margin : 0px;
  padding : 2px 5px;
  cursor : default;
  display : block;
  width: 100%;
  font : menu;
  font-size : 12px;
  overflow : hidden;
  border-bottom : 1px dotted #888;
}

.ac_loading {
  background : url(/wo/images/indicator.gif) right center no-repeat;
}

.ac_over {
  background-color : Highlight;
  color : HighlightText;
}

.popup {
  position : absolute;
  left : 0px;
  top : 0px;
  width: 132px;
  border-style : solid;
  border-width: 4px;
  border-color : blue;
  background-color : yellow;
  padding : 5px;
  color : red;
  font-family : Arial;
  font-weight : bold;
  font-size : 10pt;
  z-index : 2;
  visibility : hidden;
}


.form_box {
  border:1px dashed #bbbbbb;
  padding:10px;
  width:90%;
  background-color:#dddddd;
}

#registered_box {
  text-align:center; 
  font-weight:bold; 
  color:blue; 
  margin-bottom: 2em;
}
#waitlisted_box {
  text-align:center; 
  font-weight:bold; 
  background-color:#a22c22; 
  color:#ffffff; margin-bottom: 2em;
}


<!-- ==================== CONTACT INFO ==================== -->

#contact_record {
   width : 720px;
   background-color : #ddd;   
}

#contact_record td, #contact_record th {
  padding : 3px;
  font-size : 0.8em;
}
#contact_record th {
  width : 200px;
  background-color : #ddd; 
}
#contact_record td {
  width : 510px;
  background-color : #eee; 
}

#contact_profile_update_button {
  display : block;
  float : right;
  text-decoration : none;
  background-color : #888;
  font-size : 1.2em;
  padding : 3px 4px;
  border : 1px solid #fff;
  margin : 5px;
  color : #fff;
}
#contact_profile_update_button:hover {
  background-color : #006;
}

/* =========================TABS=========================== */

a.tablink,a.tabselect{
  border-left:1px solid #888;
  border-right:1px solid #888;
  border-top:1px solid #888;
  color:#000;

  float:left;
  padding:2px 1em;
  text-decoration:none;
  font-size:1em;
  margin:0px;
}

a.tabselect:hover, a.tablink:hover{
  background-color:#888;
  color:#fff;
  border-bottom:1px solid #888;
}

div.tabspacer{
  background-color:#fff;
  padding:2px 1em;
  text-decoration:none;
  font-size:1em;
  margin:0px;
  border-top:1px solid #fff;
  border-bottom:1px solid #888;
}

a.tabselect {
  border-bottom:1px solid #fff;
  background-color: #fff;
}

a.tablink{
  background-color:#E5D8DF;
  border-bottom:1px solid #888;
}

div.tabfolder{
  border-left:1px solid #888;
  border-right:1px solid #888;
  border-bottom:1px solid #888;
  padding:10px;
}

.event_info {
  font-size: 11px;
  padding-left: 20px;
}
.event_header {
  width: 200px;
  font-weight: bold;
  font-size:14px;
  color: #24486C;
  padding: 3px;
  text-align: right;
  background-color: #eeeeee;
}

/* ========================= INDEX GEO =========================== */
.geo_header {
    font-weight: bold;
    font-size:12px;
    color: #24486C;
    padding-bottom: 5px;
    border-bottom:1px solid #cccccc;
}

.geo_event_wrap {
    width: 100%;
}

.geo_event {
  padding-left: 20px;
  font-size: 11px;
  width: 100%;
}
.geo_language {
  width: 100px;
}




.geo_date {
  color: #6699CC;
}
.geo_location {
  color: #000000;
}
.geo_country {
  color: #666666;
}
.section_break {
  height: 20px;
}

.geo_city {
  color: #000000;
}

.geo_header_date {
  font-weight: bold;
  font-size:12px;
  color: #24486C;
  padding-bottom: 5px;
  text-align: right;
}

.agenda_time {
  color: #6699CC;
  width: 150px;
  padding-right:10px;
}
.agenda_description {
  color: #000000;
  width: 400px;
  font-size: 11px;
}
.event_agenda_table {
  width: 500px;
  padding-left: 20px;
}



/* =========================INDEX=========================== */
#EVENT_LIST_HEADING {
  margin-top : 2em;
  font-size:16px; 
  font-weight:bold; 
  background-color:#e2e2e2; 
  color:#a22c22; 
  padding:5px;
}

#event_listing {
  padding-left:30px; 
  padding-right:30px; 
  padding-top:30px;
}

#REGISTRATION_SUPPORT_BOX {
  width:250px; 
  font-face:verdana;
  float : right;
  background-color:#dddddd; 
  padding:3px;
  margin: 1em 0px 1em 2em;

}



#REGISTRATION_SUPPORT_BOX_1 {
  height:20px; 
  background-color:#0F1E3D;
  color:#FFFFFF; 
  padding:2px; 
  padding-right:5px; 
  text-align:right; 
  border-bottom:3px solid #dddddd;
}

#REGISTRATION_SUPPORT_BOX_2 {
  background-color:#dddddd;
}

#REGISTRATION_SUPPORT_BOX_3 {
  height:60px; 
  background-color:#0A5284; 
}

#REGISTRATION_SUPPORT_BOX_4 {
  padding-top : 10px;
  color:#ffffff;
  font-size:18px; 
  text-align:right;
  padding-right:30px; 
  font-weight:normal;"
}
#REGISTRATION_SUPPORT_BOX_5 {
  color:#000000;
  font-size:12px;
  text-align:right;
  padding-right:30px;
}

.registration_notice {
  padding:5px; background-color:#990000; color:#fff; font-weight:bold;
}

/* ========================= REGISTRATION STEPS =========================== */

.stepwrap {
  float: left;
  text-align: center;
  font-size: 10px;
  padding: 5px; 
}
.stepbox {
  border: 1px solid #ddd;
  padding: 3px;
  width: 90px; 
} 

.stepbox_off {
  background-color: #fff;
  color: #000000;
  font-size: 9px; 
}

.stepbox_on {
  /* background-color: #990000; */
  background-color: #0f0;
  background-image: url(/images/semi-transparent.gif);

  color: #990000;
  font-size: 9px; 
}

.stepbox_completed {
  background-color: #888; 
  background-image: url(/images/semi-transparent.gif);
  font-size: 9px;
}

.cancel_link {
   text-transform:none;
   background:url(/images/unchecked.gif) no-repeat 0 2px;
   padding-left : 17px;   
}


.register_link {
   text-transform:none;
   background:url(/images/checked.gif) no-repeat 0 2px;
   padding-left : 17px;   
}

/* --------------- SPONSOR -------------- */
#sponsor_name {
  float : right;
  margin-right : 1em;
  padding : 5px;
  border : 1px dotted #888;
}

a.registernow {
   padding:60px 16px;
   margin:0 0 0 26px;
   position:relative;
   top:112px;
   background:url(../images/event_unique/90_index_registernow.jpg)no-repeat left;
   text-decoration:none;
}

a.registernow:hover {
   background-position:right;
   margin:0 0 0 30px;
}

a.registernow span {
   position:fixed;
   left:-1000px;
}
a.attendnow {
   padding:60px 16px;
   margin:0 0 0 26px;
   position:relative;
   top:112px;
   background:url(../images/event_unique/90_index_attendnow.jpg)no-repeat left;
   text-decoration:none;
}

a.attendnow:hover {
   background-position:right;
   margin:0 0 0 30px;
}

a.attendnow span {
   position:fixed;
   left:-1000px;
}

/* ================= STORE ================= */
#catalog h2 {
  clear : both;
  border-top:1px solid #888;
  border-left:1px solid #888;
  border-right:1px solid #888;
  color: #036;
  padding : 0.51em;
  background-color: #eef;
  margin-bottom:0px;
}

.catalog_section {
  width : 700px;
}

.catalog_price {
  font-weight : bold;
}

div.item {
  border : 1px solid #888;
  padding : 1em;
}

#ordercontent {
  font-size : 0.9em;
}

#ordercontent th {
  white-space : normal;
}

#ordercontent th {
  background-color : #aaf;
}

#cartbuttons {
  text-align : center;
  margin-top : 10px;
}

#error {
  margin : 10px auto;
  border : 2px solid #f00;
  background-color : #f88;
  padding : 0.5em;
  width : 300px;
  text-align : center;
}

#shoppingcart {
  background-color : #77f;
  -moz-border-radius : 5px;
  -webkit-border-radius: 8px;
}

#shoppingcart td {
  background-color : #fff;
  padding : 1px 3px;
}

#shoppingcart td.cart_input {
  background-color : #ff7;
}

#shoppingcart th {
  background-color : #aaf;
  color : #006;
  padding : 1px 3px;
}

span.cart_convert {
  color : #f00;
  background-color : #eee;
}

#cart_note {
  font-size : 0.8em;
  color : #f00;
}

.submit_button, .checkout_button, .return_button {
  font-size : 1em;
  padding : 0.25em 0.4em;
  color : #fff;
  font-weight : bold;
  font-size : 1.2em;
  border : 1px solid #fff;
  cursor : pointer;
  background-color : #939;
  text-decoration : none;
  -moz-border-radius : 5px;
  -moz-box-shadow: #888 2px 2px 2px;
  -webkit-box-shadow: #888 2px 2px 2px;
  -webkit-border-radius: 8px;
}

.return_button  {
  background-color : #ff7;
  color : #000;
}

.checkout_button {
  background-color : #6f6;
  color : #000;
}

.submit_button:hover, .checkout_button:hover, .return_button:hover {
  background-color : #77f;
}

.checkout_button:hover {
  background-color : #080;
  color : #fff;
}


#cart_location_currency {
  margin-bottom : 20px;
}

.order_fieldset legend {
  font-size : 1em;
  background-color : #fff;
  padding : 10px;
  border : 1px solid #888;
  -moz-border-radius : 4px;
  -webkit-border-radius: 4px;
  font-weight : bold;
}


.order_fieldset {
  background-color : #ffc;
  margin-bottom : 2em;
  border : 1px solid #939;
  -moz-border-radius : 8px;
  -webkit-border-radius: 8px;
  padding : 10px;
}

#cart_location_currency {
  border : 1px solid #77f;
  width : 500px;
  -moz-border-radius : 6px;
  -webkit-border-radius: 6px;
  background-color : #aaf;
}

td#cart_total  {
  background-color : #ff7;
  font-weight : bold;
}

#previous_order_fieldset b {
  font-size : 0.8em;
  text-align : center;
}

#previous_order_fieldset .checkout_button {
  padding : 0px;
  font-weight : normal;
}

#cart_overlay {
  position : absolute;
  background-image: url(/images/overlay.png);
  margin-top : 0px;
  margin-left : 0px;
  height : 800px;
  width : 100%;
}

#shopping_cart_form {
  margin : 10px auto;
  width : 800px;
  background-color : #fff;
  padding : 10px;
  -moz-border-radius : 20px;
  -webkit-border-radius: 20px;
}


#shopping_cart_close {
  display : bock;
  float : right;
  font-size : 20px;
  text-decoration : none;
  color : #fff;
  background-color : #888;
  padding : 0px 5px;
  margin : 0px;
  font-family : Arial, helvetica, san serif;
  font-weight : bold;
  border : 1px solid #555;
  -moz-border-radius : 15px;
  -webkit-border-radius: 15px;
}


#shopping_cart_close:hover {
  background-color : #ccc;
  color : #f00;
}


.store_item {
  border : 1px dotted #888;
  padding : 1em;
  margin-bottom : 8px;
}

.store_item_image {
  float : right;
  margin-left : 10px;
  margin-bottom : 10px;
}

.store_item_title {
  font-size : 1.2em;
  font-weight : bold;
  color : #008
}
.store_item_description {
  padding : 1em 0px;
}
.store_item_price {
  font-style : italic;
}

.store_order_button_div {
  padding : 5px 0px;
}

#store_header h1 {
  color : #fff;
}

#button_view_cart {
  font-size : 90%;
  text-decoration : none;
  background-color : #fff;  
  color : #006;
  margin : 0px;
  padding : 3px 1em;
  text-align : center;
  white-space : nowrap;
  -moz-border-radius : 5px;
  -moz-box-shadow : #555 2px 2px 2px;
  -webkit-border-radius: 5px;
  -webkit-box-shadow: #555 2px 2px 2px;
  background: -moz-linear-gradient(top, white, gold);
  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,white), color-stop(100%,gold));
}

#button_view_cart img{
  position: relative;
  top : 3px;
}

#button_view_cart:active {
  background-color : #6f6;
}

#button_view_cart:hover {
  color : #fff;
  background: -moz-linear-gradient(top, white, green);
  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,white), color-stop(100%,green));
}

a.orderbutton {
  text-decoration : none;
  display : block;
  width : 8em;
  border : 1px solid;
  border-color : #ddd #555 #111 #ccc;
  background-color : #d00;
  color : #fff;
  font-weight : bold;
  padding : 3px 1em;
  text-align : center;
}

a.orderbutton:hover {
  background-color : #0c0;
  color : #fff;
}


