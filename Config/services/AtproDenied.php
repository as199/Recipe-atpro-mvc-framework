<?php

namespace Atpro\mvc\Config\services;

use Exception;
use Atpro\mvc\core\AbstractController;

class AtproDenied extends AbstractController
{
    /**
     * @author Assane Dione <atpro0290@gmail.com>
     * @param string $smg
     * @return void
     */
    public function denied($smg = '')
    {
        header('HTTP/1.0 401 Unauthorized ');
        return throw new Exception('
        <style>
             .page_404{ padding: 0 !important; color:#fff; background-color: rgb(109, 125, 128);}

            .four_zero_four_bg{
                background-size: cover;
                background-position: center;
                padding:0;
                height: 100vh;
                display: flex;
                justify-content:center;
            }


            .four_zero_four_bg h1{
                font-size:80px;
                align: center;
            }
        </style>
        <section class="page_404">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 ">
                    <div class="col-sm-10 col-sm-offset-1  text-center">
                        <div class="four_zero_four_bg">
                            <h1 class="text-center ">401 Unauthorized Access Denied</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>');
    }

    /**
     *@author Assane Dione <atpro0290@gmail.com>
     *
     * @return void
     */
    public function notFound()
    {
        header('HTTP/1.0 404 Not Found ');
         return throw new Exception('
        <style>
            .page_404{ padding: 0 !important; color:#fff; background-color: rgb(109, 125, 128);}
            .four_zero_four_bg{
                background-size: cover;
                background-position: center;
                padding:0;
                height: 100vh;
                display: flex;
                justify-content:center;
            }

            .four_zero_four_bg h1{
                font-size:80px;
                align: center;
            }
        </style>
        <section class="page_404">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 ">
                    <div class="col-sm-10 col-sm-offset-1  text-center">
                        <div class="four_zero_four_bg">
                            <h1 class="text-center ">404 Page Not Found!</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>');
    }
    public function getAccess(): array
    {
        return [];
    }
}
