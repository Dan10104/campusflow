<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Certificado</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=UnifrakturCook:wght@700&display=swap" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Open+Sans|Pinyon+Script|Rochester');

        .unifrakturcook-bold {
            font-family: "UnifrakturCook", cursive;
            font-weight: 700;
            font-style: normal;
        }

        .cursive {
            font-family: 'Pinyon Script', cursive;
        }

        .sans {
            font-family: 'Open Sans', sans-serif;
        }

        .bold {
            font-weight: bold;
        }

        .block {
            display: block;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 1122px;
            height: 794px;
        }


        .underline {
            border-bottom: 1px solid #777;
            padding: 5px;
            margin-bottom: 15px;
        }

        .margin-0 {
            margin: 0;
        }

        .padding-0 {
            padding: 0;
        }

        body {
            font-family: sans-serif;
            background-color: #618597;
            margin: 0;
            padding: 30px;
            color: #333;
        }

        .pm-certificate-container {
            position: relative;
            width: 9000px;
            height: 60px;
            margin-left: 0;
            margin-right: 0;
            background-color: #618597;
            padding: 30px;
            color: #333;
            font-family: 'Open Sans', sans-serif;
            box-shadow: 0 0 5px rgba(0, 0, 0, .5);
        }

        .outer-border {
            width: 1060px;
            height: 730px;
            position: absolute;
            left: 0;
            top: 0;
            border: 2px solid #fff;
        }

        .inner-border {
            width: 1000px;
            height: 670px;
            position: absolute;
            left: 30px;
            top: 30px;
            border: 2px solid #fff;
        }

        .pm-certificate-border {
            position: relative;
            width: 940px;
            height: 610px;
            padding: 0;
            border: 1px solid #E1E5F0;
            background-color: rgba(255, 255, 255, 1);
            background-image: none;
            left: 30px;
            top: 30px;
        }

        .pm-certificate-block {
            width: 870px;
            position: relative;
            left: 50%;
            margin-left: -325px;
            top: 30px;
            margin-top: 0;
        }

        .pm-certificate-header {
            margin-bottom: 10px;
        }

        .pm-certificate-title {
            position: relative;
            top: 40px;
            text-align: center;
        }

        h2 {
            font-size: 34px !important;
        }


        .pm-certificate-body {
            padding: 20px;
        }

        .pm-name-text {
            font-size: 30px;
            text-align: center;
        }

        .pm-earned-text {
            font-size: 30px;
        }

        .pm-credits-text {
            font-size: 35px;
        }


        .pm-earned-text {
            font-size: 30px;
        }

        .pm-earned-text {
            font-size: 30px;
        }

        .pm-credits-text {
            font-size: 20px;
        }

        .underline {
            margin-bottom: 5px;
        }

        * {
            box-sizing: border-box;
        }
    </style>
</head>

<body>
    <div class="container pm-certificate-container">
        <div class="outer-border"></div>
        <div class="inner-border"></div>

        <div class="pm-certificate-border col-xs-12">
            <div class="row pm-certificate-header">
                <div class="pm-certificate-title unifrakturcook-bold  col-xs-12 text-center">
                    <h2>Certificado de Finalización</h2>
                </div>
            </div>

            <div class="row pm-certificate-body">
                <div class=" row pm-certificate-block">
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-2"><!-- LEAVE EMPTY --></div>

                            <div class="pm-certificate-name margin-0 col-xs-8 text-center">
                                <span class="pm-earned-text padding-0 block cursive">Reconoce que</span>
                                <span class="pm-name-text bold">{{ $estudiante->nombre }} {{ $estudiante->apellido }}</span>
                            </div>
                            <div class="col-xs-2"><!-- LEAVE EMPTY --></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row pm-certificate-body">
                <div class=" row pm-certificate-block">
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-2"><!-- LEAVE EMPTY --></div>

                            <div class="pm-certificate-name margin-0 col-xs-8 text-center">

                                <span class="pm-credits-text block bold sans" style="margin-bottom: 30px !important;">Ha completado y aprobado el curso {{ $curso->nombre }}</span>
                                <span class="pm-credits-text block bold sans">En la ciudad de Santa Cruz de la Sierra el día {{ $fecha }}</span>
                            </div>
                            <div class="col-xs-2"><!-- LEAVE EMPTY --></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row pm-certificate-body">
                <div class=" row pm-certificate-block">
                    <div class="col-xs-12">
                        <div class="row">
                            <table width="600px" style="margin-top: 50px;">
                                <tr>
                                    <td align="center" style="width: 33%;">
                                        <div style="border-bottom: 1px solid #000; height: 30px; margin-bottom: 5px;"></div>
                                        <strong>Encargado del curso</strong>
                                    </td>
                                    <td style="width: 34%;"></td>
                                    <td align="center" style="width: 33%;">
                                        <div style="border-bottom: 1px solid #000; height: 30px; margin-bottom: 5px;"></div>
                                        <strong>Coordinador</strong>
                                    </td>
                                </tr>
                            </table>


                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</body>

</html>