/**
 * Created by mohamedchakiri on 09/12/2017.
 */
function addFields(){
    var container = document.getElementById("container");
    //Boolean pour verifier si la div est remplie ou non
    var haschilds = container.hasChildNodes();
    var childs = container.childNodes;
    var num = childs.length/3;
    //texte
    container.appendChild(document.createTextNode("Etape " + (num+1)));
    var input = document.createElement("input");
    input.type = "text";
    input.name = "etape" + (num+1);
    input.id = "etape" + (num+1);
    input.className = "autocomplete";
    container.appendChild(input);
    container.appendChild(document.createElement("br"));
    //Autocomplete des input
    initAutocomplete();
}