 $(function () {
    $('.js-sweetalert button').on('click', function () {
       if (type === 'error' ) {
           swal({
               title: "Error",
               text: "Erreur d'identifiant ou de mot de passe !",
               type: "error",
               confirmButtonText: "OK"
           });
       }else{
               swal({
                   title: "Succes",
                   text: "Vous êtes bien connecté !",
                   type: "succes",
                   confirmButtonText: "OK"
               });
           }
    }



