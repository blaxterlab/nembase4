function checkAll(){for (i=0; i<document.forms[0].length; i++)
	if(document.forms[0].elements[i].name=="All[]") document.forms[0].elements[i].checked = true
	checkCladeI();checkRhabditina()
}
function clearAll(){for (i=0; i<document.forms[0].length; i++)
	if(document.forms[0].elements[i].name=="All[]") document.forms[0].elements[i].checked = false
	clearCladeI();clearRhabditina()
}
function checkCladeI(){for (i=0; i<document.forms[0].length; i++)
	if(document.forms[0].elements[i].name=="I[]") document.forms[0].elements[i].checked = true
	checkTrichinellida();
}
function clearCladeI(){for (i=0; i<document.forms[0].length; i++)
	if(document.forms[0].elements[i].name=="I[]") document.forms[0].elements[i].checked = false
	clearTrichinellida()
}
function checkRhabditina(){for (i=0; i<document.forms[0].length; i++)
	if(document.forms[0].elements[i].name=="Rhabditina[]") document.forms[0].elements[i].checked = true
	checkIII();checkIV();checkV();checkIVa();checkIVb()
}                                          
function clearRhabditina(){for (i=0; i<document.forms[0].length; i++)
	if(document.forms[0].elements[i].name=="Rhabditina[]") document.forms[0].elements[i].checked = false
	clearIII();clearIV();clearV();clearIVa();clearIVb()
}
function checkIII(){for (i=0; i<document.forms[0].length; i++)
	if(document.forms[0].elements[i].name=="III[]") document.forms[0].elements[i].checked = true
	checkAscarididomorpha();checkSpiruromorpha()
}
function clearIII(){for (i=0; i<document.forms[0].length; i++)
	if(document.forms[0].elements[i].name=="III[]") document.forms[0].elements[i].checked = false
	clearAscarididomorpha();clearSpiruromorpha()
}
function checkIV(){for (i=0; i<document.forms[0].length; i++)
	if(document.forms[0].elements[i].name=="IV[]") document.forms[0].elements[i].checked = true
	checkStrongyloidoidea();checkTylenchomorpha()
}
function clearIV(){for (i=0; i<document.forms[0].length; i++)
	if(document.forms[0].elements[i].name=="IV[]") document.forms[0].elements[i].checked = false
	clearStrongyloidoidea();clearTylenchomorpha()
}
function checkV(){for (i=0; i<document.forms[0].length; i++)
	if(document.forms[0].elements[i].name=="V[]") document.forms[0].elements[i].checked = true
	checkStrongylomorpha()
}
function clearV(){for (i=0; i<document.forms[0].length; i++)
	if(document.forms[0].elements[i].name=="V[]") document.forms[0].elements[i].checked = false
	clearStrongylomorpha()
}
function checkTrichinellida(){for (i=0; i<document.forms[0].length; i++)if(document.forms[0].elements[i].name=="Trichinellida[]") document.forms[0].elements[i].checked = true}
function clearTrichinellida(){for (i=0; i<document.forms[0].length; i++)if(document.forms[0].elements[i].name=="Trichinellida[]") document.forms[0].elements[i].checked = false}
function checkAscarididomorpha(){for (i=0; i<document.forms[0].length; i++)if(document.forms[0].elements[i].name=="Ascarididomorpha[]") document.forms[0].elements[i].checked = true}
function clearAscarididomorpha(){for (i=0; i<document.forms[0].length; i++)if(document.forms[0].elements[i].name=="Ascarididomorpha[]") document.forms[0].elements[i].checked = false}
function checkSpiruromorpha(){for (i=0; i<document.forms[0].length; i++)if(document.forms[0].elements[i].name=="Spiruromorpha[]") document.forms[0].elements[i].checked = true}
function clearSpiruromorpha(){for (i=0; i<document.forms[0].length; i++)if(document.forms[0].elements[i].name=="Spiruromorpha[]") document.forms[0].elements[i].checked = false}
function checkStrongyloidoidea(){for (i=0; i<document.forms[0].length; i++)if(document.forms[0].elements[i].name=="Strongyloidoidea[]") document.forms[0].elements[i].checked = true}
function clearStrongyloidoidea(){for (i=0; i<document.forms[0].length; i++)if(document.forms[0].elements[i].name=="Strongyloidoidea[]") document.forms[0].elements[i].checked = false}
function checkTylenchomorpha(){for (i=0; i<document.forms[0].length; i++)
	if(document.forms[0].elements[i].name=="Tylenchomorpha[]") document.forms[0].elements[i].checked = true
	checkMeloidogyne()
}
function clearTylenchomorpha(){for (i=0; i<document.forms[0].length; i++)
	if(document.forms[0].elements[i].name=="Tylenchomorpha[]") document.forms[0].elements[i].checked = false
	clearMeloidogyne()
}
function checkStrongylomorpha(){for (i=0; i<document.forms[0].length; i++)if(document.forms[0].elements[i].name=="Strongylomorpha[]") document.forms[0].elements[i].checked = true}
function clearStrongylomorpha(){for (i=0; i<document.forms[0].length; i++)if(document.forms[0].elements[i].name=="Strongylomorpha[]") document.forms[0].elements[i].checked = false}
function checkMeloidogyne(){for (i=0; i<document.forms[0].length; i++)if(document.forms[0].elements[i].name=="Meloidogyne[]") document.forms[0].elements[i].checked = true}
function clearMeloidogyne(){for (i=0; i<document.forms[0].length; i++)if(document.forms[0].elements[i].name=="Meloidogyne[]") document.forms[0].elements[i].checked = false}
