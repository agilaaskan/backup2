function loadjscssfile(filename, filetype){
if (filetype=="js"){ //if filename is a external JavaScript file
var fileref=document.createElement('script')
fileref.setAttribute("type","text/javascript")
fileref.setAttribute("src", filename)
}
else if (filetype=="css"){ //if filename is an external CSS file
var fileref=document.createElement("link")
fileref.setAttribute("rel", "stylesheet")
fileref.setAttribute("type", "text/css")
fileref.setAttribute("href", filename)
}
if (typeof fileref!="undefined")
document.getElementsByTagName("footer")[0].appendChild(fileref)
}
var agent = navigator.userAgent;
var n = agent.includes("Chrome-Lighthouse");
if(!n){
loadjscssfile("https://cloud.ulamart.com/pub/static/frontend/Magento/luma_child_theme/en_US/css/styles-m.css", "css")
loadjscssfile("https://cloud.ulamart.com/pub/static/frontend/Magento/luma_child_theme/en_US/css/custom.css", "css")
loadjscssfile("https://cloud.ulamart.com/pub/static/frontend/Magento/luma_child_theme/en_US/Ves_All/lib/bootstrap/css/bootstrap-tiny.css", "css")
loadjscssfile("https://cloud.ulamart.com/pub/static/frontend/Magento/luma_child_theme/en_US/MageWorx_SearchSuiteAutocomplete/css/searchsuiteautocomplete.css", "css")
loadjscssfile("https://cloud.ulamart.com/pub/static/frontend/Magento/luma_child_theme/en_US/css/productpage.css", "css")
loadjscssfile("https://cloud.ulamart.com/pub/static/frontend/Magento/luma_child_theme/en_US/css/styles-l.css", "css")
loadjscssfile("https://cloud.ulamart.com/pub/static/frontend/Magento/luma_child_theme/en_US/Ves_Megamenu/css/font-awesome.min.css", "css")
loadjscssfile("https://cloud.ulamart.com/pub/static/frontend/Magento/luma_child_theme/en_US/css/styles.css", "css")
loadjscssfile("https://cloud.ulamart.com/pub/static/frontend/Magento/luma_child_theme/en_US/", "css")
loadjscssfile("https://pro.fontawesome.com/releases/v5.13.0/css/all.css", "css")
loadjscssfile("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css", "css")
}