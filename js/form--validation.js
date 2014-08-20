function checkReferrerAvailability11(str, url)
{
    if (isSpclChar(str, 'sponsor') == false)
    {
        document.getElementById('sponsor').focus();
        return false;
    }
    var xmlhttp;
    if (str == "")
    {
        alert("Please enter the sponsor name.");
        document.getElementById('sponsor').focus();
        return false;
    }

    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function()
    {
        if (xmlhttp.status == 200 && xmlhttp.readyState == 4)
        {
            document.getElementById("check_referrer").innerHTML = xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET", url + "?action=sponsor&q=" + str, true);
    xmlhttp.send();

}

function numeric(x, id)
{
    //var x = document.sform.phone_no.value;
    if (isNaN(x) || x.indexOf(" ") != -1)
    {
        alert("You have entered wrong " + id + " number.");
        document.getElementById(id).value = '';
        document.getElementById(id).focus();
        return false;
    }
    return true;
}

function isSpclChar(char, id)
{
    var iChars = "~`!@#$%^&*()+=-[]\\\';,. /{}|\":<>?";
    for (var i = 0; i < char.length; i++)
    {
        if (iChars.indexOf(char.charAt(i)) != -1)
        {
            alert("Special characters are not allowed in " + id + ".");
            document.getElementById(id).value = '';
            document.getElementById(id).focus();
            return false;
        }
    }
    return true;
}

function checkname(char, id)
{
    var iChars = "~`!@#$%^&*()+=[]\\\';,./{}|\":<>?";
    for (var i = 0; i < char.length; i++)
    {
        if (iChars.indexOf(char.charAt(i)) != -1)
        {
            alert("Special characters are not allowed in " + id + ".");
            document.getElementById(id).value = '';
            document.getElementById(id).focus();
            return false;
        }
    }
    return true;
}

function allowspace(char, id)
{
    var iChars = "~`!@#$%^&*()+=[]\\\';./{}|\":<>?";
    for (var i = 0; i < char.length; i++)
    {
        if (iChars.indexOf(char.charAt(i)) != -1)
        {
            alert("Special characters are not allowed in " + id + ".");
            document.getElementById(id).value = '';
            document.getElementById(id).focus();
            return false;
        }
    }
    return true;
}

function emailValidation()
{
    //var reg= /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    var x = document.getElementById('email').value;
    //alert(x);
    var atPos = x.indexOf("@");
    var dotPos = x.lastIndexOf(".");
    if (atPos < 1 || dotPos <= atPos + 1 || dotPos + 2 >= x.length)
            //if(reg.test(x)==false)
            {
                alert("Your email address is not valid.");
                return false;
            }
    return true;
}
function formValidation()
{
    var username = document.getElementById('username').value;
    var password = document.getElementById('password').value;
    var confirm_password = document.getElementById('confirm_password').value;
    var sponsor = document.getElementById('sponsor').value;
    var firstname = document.getElementById('firstname').value;
    var lastname = document.getElementById('lastname').value;
    var address1 = document.getElementById('address1').value;
    var city = document.getElementById('city').value;
    var state = document.getElementById('state').value;
    var postalcode = document.getElementById('postalcode').value;
    var country = document.getElementById('country').value;
    var email = document.getElementById('email').value;
    var confirm_email = document.getElementById('confirm_email').value;
    var telephone = document.getElementById('telephone').value;
    var dob = document.getElementById('dob').value;
    var epin = document.getElementById('epin').value;

    if (username == '')
    {
        alert("Please enter username.");
        document.getElementById('username').focus();
        return false;
    }

    /*if(epin=='')
     {
     alert("Please Enter the ePin.");
     document.getElementById('epin').focus();
     return false;
     }*/


    if (password == '')
    {
        alert("Please enter password.");
        document.getElementById('password').focus();
        return false;
    }
    if (password.length < 6)
    {
        alert("Password length is atleast 6 char.");
        document.getElementById('password').focus();
        return false;
    }
    if (confirm_password == '')
    {
        alert("Please confirm your password.");
        document.getElementById('confirm_password').focus();
        return false;
    }
    if (password != confirm_password)
    {
        alert("Your password does not match please try again.");
        document.getElementById('confirm_password').focus();
        return false;
    }


    if (sponsor == '')
    {
        alert("Please enter sponsor name.");
        document.getElementById('sponsor').focus();
        return false;
    }
    if (document.getElementById("left").checked == false && document.getElementById("right").checked == false)
    {
        alert("Please select the placement.");
        document.getElementById("left").focus();
        return false;
    }
    if (firstname == '')
    {
        alert("Please enter your first name.");
        document.getElementById('firstname').focus();
        return false;
    }
    if (lastname == '')
    {
        alert("Please enter your last name.");
        document.getElementById('lastname').focus();
        return false;
    }
    if (address1 == '')
    {
        alert("Please enter your address.");
        document.getElementById('address1').focus();
        return false;
    }
    if (city == '')
    {
        alert("Please enter your city name.");
        document.getElementById('city').focus();
        return false;
    }
    if (state == '')
    {
        alert("Please enter your state name.");
        document.getElementById('state').focus();
        return false;
    }
    if (postalcode == '')
    {
        alert("Please enter your postal code.");
        document.getElementById('postalcode').focus();
        return false;
    }
    if (country == '')
    {
        alert("Please select your country name.");
        document.getElementById('country').focus();
        return false;
    }
    if (email == '')
    {
        alert("Please enter your E-mail address.");
        document.getElementById('email').focus();
        return false;
    }
    if (!emailValidation())
    {
        document.getElementById("email").focus();
        return false;
    }
    if (confirm_email == '')
    {
        alert("Please confirm your E-mail address.");
        document.getElementById('confirm_email').focus();
        return false;
    }
    if (email != confirm_email)
    {
        alert("Your E-mail address does not match please try again.");
        document.getElementById('confirm_email').focus();
        return false;
    }
    if (telephone == '')
    {
        alert("Please enter your contact number.");
        document.getElementById('telephone').focus();
        return false;
    }
    if (dob == '')
    {
        alert("Please enter your date of birth.");
        document.getElementById('dob').focus();
        return false;
    }
}

function adminFormValidation()
{
    var username = document.getElementById('username').value;
    var password = document.getElementById('password').value;
    var confirm_password = document.getElementById('confirm_password').value;
    var firstname = document.getElementById('firstname').value;
    var lastname = document.getElementById('lastname').value;
    var address1 = document.getElementById('address1').value;
    var city = document.getElementById('city').value;
    var state = document.getElementById('state').value;
    var postalcode = document.getElementById('postalcode').value;
    var country = document.getElementById('country').value;
    var email = document.getElementById('email').value;
    var confirm_email = document.getElementById('confirm_email').value;
    var telephone = document.getElementById('telephone').value;
    var dob = document.getElementById('dob').value;
    var epin = document.getElementById('epin').value;


    if (username == '')
    {
        alert("Please enter username.");
        document.getElementById('username').focus();
        return false;
    }
    if (epin == '')
    {
        alert("Please Enter the ePin.");
        document.getElementById('epin').focus();
        return false;
    }
    if (password == '')
    {
        alert("Please enter password.");
        document.getElementById('password').focus();
        return false;
    }
    if (password.length < 6)
    {
        alert("Password length is atleast 6 char.");
        document.getElementById('password').focus();
        return false;
    }
    if (confirm_password == '')
    {
        alert("Please confirm your password.");
        document.getElementById('confirm_password').focus();
        return false;
    }
    if (password != confirm_password)
    {
        alert("Your password does not match please try again.");
        document.getElementById('confirm_password').focus();
        return false;
    }
    if (firstname == '')
    {
        alert("Please enter your first name.");
        document.getElementById('firstname').focus();
        return false;
    }
    if (lastname == '')
    {
        alert("Please enter your last name.");
        document.getElementById('lastname').focus();
        return false;
    }
    if (address1 == '')
    {
        alert("Please enter your address.");
        document.getElementById('address1').focus();
        return false;
    }
    if (city == '')
    {
        alert("Please enter your city name.");
        document.getElementById('city').focus();
        return false;
    }
    if (state == '')
    {
        alert("Please enter your state name.");
        document.getElementById('state').focus();
        return false;
    }
    if (postalcode == '')
    {
        alert("Please enter your postal code.");
        document.getElementById('postalcode').focus();
        return false;
    }
    if (country == '')
    {
        alert("Please select your country name.");
        document.getElementById('country').focus();
        return false;
    }
    if (email == '')
    {
        alert("Please enter your E-mail address.");
        document.getElementById('email').focus();
        return false;
    }
    if (!emailValidation())
    {
        document.getElementById("email").focus();
        return false;
    }
    if (confirm_email == '')
    {
        alert("Please confirm your E-mail address.");
        document.getElementById('confirm_email').focus();
        return false;
    }
    if (email != confirm_email)
    {
        alert("Your E-mail address does not match please try again.");
        document.getElementById('confirm_email').focus();
        return false;
    }
    if (telephone == '')
    {
        alert("Please enter your contact number.");
        document.getElementById('telephone').focus();
        return false;
    }
    if (dob == '')
    {
        alert("Please enter your date of birth.");
        document.getElementById('dob').focus();
        return false;
    }
}

function updateFormValidation()
{
    var firstname = document.getElementById('firstname').value;
    var lastname = document.getElementById('lastname').value;
    var address1 = document.getElementById('address1').value;
    var city = document.getElementById('city').value;
    var state = document.getElementById('state').value;
    var postalcode = document.getElementById('postalcode').value;
    var country = document.getElementById('country').value;
    var telephone = document.getElementById('telephone').value;
    var dob = document.getElementById('dob').value;

    if (firstname == '')
    {
        alert("Please enter your first name.");
        document.getElementById('firstname').focus();
        return false;
    }
    if (lastname == '')
    {
        alert("Please enter your last name.");
        document.getElementById('lastname').focus();
        return false;
    }
    if (address1 == '')
    {
        alert("Please enter your address.");
        document.getElementById('address1').focus();
        return false;
    }
    if (city == '')
    {
        alert("Please enter your city name.");
        document.getElementById('city').focus();
        return false;
    }
    if (state == '')
    {
        alert("Please enter your state name.");
        document.getElementById('state').focus();
        return false;
    }
    if (postalcode == '')
    {
        alert("Please enter your postal code.");
        document.getElementById('postalcode').focus();
        return false;
    }
    if (country == '')
    {
        alert("Please select your country name.");
        document.getElementById('country').focus();
        return false;
    }
    if (telephone == '')
    {
        alert("Please enter your contact number.");
        document.getElementById('telephone').focus();
        return false;
    }
    if (dob == '')
    {
        alert("Please enter your date of birth.");
        document.getElementById('dob').focus();
        return false;
    }
}

function updatePassword()
{
    var password = document.getElementById('password').value;
    var confirm_password = document.getElementById('confirm_password').value;

    if (password == '')
    {
        alert("Please enter your new password.");
        document.getElementById('password').focus();
        return false;
    }
    if (password.length < 6)
    {
        alert("Password length is atleast 6 char.");
        document.getElementById('password').focus();
        return false;
    }
    if (confirm_password == '')
    {
        alert("Please again type your new password.");
        document.getElementById('confirm_password').focus();
        return false;
    }
    if (password != confirm_password)
    {
        alert("Your password does not match please try again.");
        document.getElementById('confirm_password').focus();
        return false;
    }
}

function toggleVisibility(id)
{
    var e = document.getElementById(id);
    if (e.style.display == 'block')
        e.style.display = 'none';
    else
        e.style.display = 'block';
}

function addRow(tableID)
{

    var table = document.getElementById(tableID);
    var rowCount = table.rows.length;
    var row = table.insertRow(rowCount);
    var colCount = table.rows[0].cells.length;

    for (var i = 0; i < colCount; i++)
    {
        var newcell = row.insertCell(i);
        newcell.innerHTML = table.rows[0].cells[i].innerHTML;
        //alert(newcell.childNodes);
        switch (newcell.childNodes[0].type)
        {
            case "text":
                newcell.childNodes[0].value = "";
                break;
            case "checkbox":
                newcell.childNodes[0].checked = false;
                break;
            case "select-one":
                newcell.childNodes[0].selectedIndex = 0;
                break;
        }
    }
}

function deleteRow(tableID)
{
    try
    {
        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
        for (var i = 0; i < rowCount; i++)
        {
            var row = table.rows[i];
            var chkbox = row.cells[0].childNodes[0];
            if (null != chkbox && true == chkbox.checked)
            {
                if (rowCount <= 1)
                {
                    alert("Cannot delete all the rows.");
                    break;
                }
                table.deleteRow(i);
                rowCount--;
                i--;
            }
        }
    }
    catch (e)
    {
        alert(e);
    }
}

var ids = new Array('direct_referral', 'right_referral', 'left_referral', 'pair1', 'pair2', 'initial_pair', 'initial_amount', 'further_amount', 'currency', 'bonus_criteria', 'username', 'password', 'confirm_password', 'email', 'confirm_email');
var values = new Array('', '', '', '', '', '');

function populateArrays()
{
    // assign the default values to the items in the values array
    for (var i = 0; i < ids.length; i++)
    {
        var elem = document.getElementById(ids[i]);
        if (elem)
            if (elem.type == 'checkbox' || elem.type == 'radio')
                values[i] = elem.checked;
            else
                values[i] = elem.value;
    }
}



var needToConfirm = true;

window.onbeforeunload = confirmExit;
function confirmExit()
{
    if (needToConfirm)
    {
        // check to see if any changes to the data entry fields have been made
        for (var i = 0; i < values.length; i++)
        {
            var elem = document.getElementById(ids[i]);
            if (elem)
                if ((elem.type == 'checkbox' || elem.type == 'radio')
                        && values[i] != elem.checked)
                    return "The changes you made will be lost if you navigate away from this page. \n Are you sure you want to leave this page?";
                else if (!(elem.type == 'checkbox' || elem.type == 'radio') &&
                        elem.value != values[i])
                    return "The changes you made will be lost if you navigate away from this page.";
        }

        // no changes - return nothing      
    }
}
  