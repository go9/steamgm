<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">


<title>
    SteamGM
    @yield("title")
</title>

<!-- JQuery -->
<script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
        crossorigin="anonymous"></script>

<!-- Bootstrap 4 -->

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>

<!-- Bootstrap 4 Theme
<link rel="stylesheet" href="https://bootswatch.com/lumen/bootstrap.css">-->

<!-- Font Awesome -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css?family=Fjalla+One|Ubuntu|Signika" rel="stylesheet">

<!-- Google Charts -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<!-- Lightbox -->
<link rel="stylesheet" href="/lightslider/src/css/lightslider.css">
<script src="/lightslider/src/js/lightslider.js"></script>

<!-- Light Gallery -->
<link rel="stylesheet" href="/lightgallery/src/css/lightgallery.css">
<script src="/lightgallery/src/js/lightgallery.js"></script>
<script src="/lightgallery/src/js/extensions.js"></script>

<!-- Stylesheets-->
<link rel="stylesheet" href="/css/normalize.css">
<link rel="stylesheet" href="/css/bootstrap_override.css">
<link rel="stylesheet" href="/css/styles.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.1/moment.min.js"></script>


<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();
    });

    function in_array(needle, haystack) {
        if (jQuery.inArray(needle, haystack) === -1) {
            return false;
        }
        else {
            return true;
        }
    }
    function stripMoney(n){
        return parseFloat(
            (n + "").replace(/[^\d.-]/g, '')
        );
    }
    function money(n) {
        if(n == null || n == 0){
            return "-";
        }
        return formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2
        }).format(
            stripMoney(n)
        );
    }

    class Icon{
        constructor(id){
            this.id = id;
        }

        wrap(options = {}){

            if(options.color == null){
                options.color = "black";
            }
            if(options.title == null){
                options.title = this.id;
            }
            return "<i" +
                " class='fa fa-" + this.id + "'" +
                " style='" +
                    "color:"+options.color+"" + ";" +
                    "'" +
                " title='"+options.title+"'" +
                ">";
        }

    }
</script>
