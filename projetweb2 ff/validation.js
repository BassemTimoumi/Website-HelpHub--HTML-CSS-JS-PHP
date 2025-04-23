function alpha(ch){
  ch=ch.toUpperCase();
  for (i=0;i<ch.length;i++){
    c=ch.charAt(i);
    if (c<"A" || c>"Z"){
      return false;
   
    };
  };
  return true;
  };
  
  function num(ch){
  ch=ch.toUpperCase();
  for (i=0;i<ch.length;i++){
    c=ch.charAt(i);
    if (c<"0" || c>"9"){
      return false;
   
    };
  };
  return true;
  };

  function checkemail(ch) {
    const atPos = ch.indexOf("@");
    const dotPos = ch.lastIndexOf(".");
  
    return atPos > 0 && dotPos > atPos + 1 && dotPos < ch.length - 1;
  }
  
  function checkidentifiantfiscal(ch) {
    return ch.length === 6 &&
           ch[0] === "$" &&
           ch[1] >= "A" && ch[1] <= "Z" &&
           ch[2] >= "A" && ch[2] <= "Z" &&
           ch[3] >= "A" && ch[3] <= "Z" &&
           !isNaN(ch[4]) &&
           !isNaN(ch[5]);
  }

function verif1(){
  nom=document.getElementById("nom").value;
  prenom=document.getElementById("prenom").value;
  cin=document.getElementById("cin").value;
  email=document.getElementById("email").value;
  pseudo=document.getElementById("pseudo").value;
  password=document.getElementById("password").value;
  
  
  if (!alpha(nom) || nom.length<3){
  alert ("verifier nom!!");
  return false;
  };
  
  if (!alpha(prenom) || prenom.length<3){
  alert ("verifier prenom!!");
  return false;
  };
  
  if (!num(cin) || cin.length!=8){
  alert ("verifier cin!!");
  return false;
  };
  
  if (!checkemail(email)){
  alert ("verifier email!!");
  return false;
  };
  
  if (!alpha(pseudo)){
  alert ("verifier pseudo!!");
  return false;
  };
  
  if (password.length < 8 || !(password.endsWith("$") || password.endsWith("#"))) {
    alert("Vérifiez le mot de passe !");
    return false;
  }
  
  
  
  };

  function verif2(){
    nom=document.getElementById("nom").value;
    prenom=document.getElementById("prenom").value;
    cin=document.getElementById("cin").value;
    email=document.getElementById("email").value;
    nom_association=document.getElementById("nom_association").value;
    nom_association=document.getElementById("adresse").value;
    identifiant_fiscal=document.getElementById("identifiant_fiscal").value;
    pseudo=document.getElementById("pseudo").value;
    password=document.getElementById("password").value;
    
    
    if (!alpha(nom) || nom.length<3){
    alert ("verifier nom!!");
    return false;
    };
    
    if (!alpha(prenom) || prenom.length<3){
    alert ("verifier prenom!!");
    return false;
    };
    
    if (!num(cin) || cin.length!=8){
    alert ("verifier cin!!");
    return false;
    };
    
    if (!checkemail(email)){
    alert ("verifier email!!");
    return false;
    };

    if (adresse.length<10 ){
      alert ("verifier adresse!!");
      return false;
      };

    if (!checkidentifiantfiscal(identifiant_fiscal) ){
      alert ("verifier identifiant fiscal!!");
      return false;
    };
    
    if (!alpha(pseudo)){
    alert ("verifier pseudo!!");
    return false;
    };
    
    if (password.length < 8 || !(password.endsWith("$") || password.endsWith("#"))) {
      alert("Vérifiez le mot de passe !");
      return false;
    }
    
    
    
    };
