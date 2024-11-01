function insertTag(tagname)
{
  if(document.getElementById('tlmiftaglist').value == "")
  {
  	document.getElementById('tlmiftaglist').value = tagname;
  }
  else
  {
  	document.getElementById('tlmiftaglist').value += ","+tagname;
  }
}