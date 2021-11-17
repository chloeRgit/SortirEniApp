function Afficher(id, buttonid)
{
    var input = document.getElementById(id);
    var button = document.getElementById(buttonid);
    if (input.type === "password")
    {
        input.type = "text";
        button.innerHTML="<i class=\"fas fa-eye-slash\"></i>";
    }
    else
    {
        input.type = "password";
        button.innerHTML="<i class=\"fas fa-eye\"></i>";
    }
}