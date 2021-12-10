<?php

namespace App\Domain\Service\Recipes;

use GuzzleHttp\Client;
use App\Domain\Repository\Recipes\ImportDupeRepository;
use App\Domain\Repository\Recipes\ImportEmealsRepository;
use App\Domain\Repository\Recipes\ImportTitleDupeRepository;
use App\Domain\Repository\Recipes\ArchiveEmealsRepository;
use App\Factory\LoggerFactory;

final class ImportForm {
    private $importDupeRepository;
    private $guzzleHTTPClient;
    private $importEmealsRepository;
    private $archiveEmealsRepository;
    private $logger;
    private $importTitleDupeRepository;

    public function __construct(
        ImportDupeRepository $importDupeRepository, 
        \GuzzleHttp\Client $guzzleHTTPClient, 
        ImportEmealsRepository $importEmealsRepository,
        ArchiveEmealsRepository $archiveEmealsRepository,
        LoggerFactory $logger,
        ImportTitleDupeRepository $importTitleDupeRepository
    ) {
        $this->importDupeRepository = $importDupeRepository;
        $this->guzzleHTTPClient = $guzzleHTTPClient;
        $this->importEmealsRepository = $importEmealsRepository;
        $this->archiveEmealsRepository = $archiveEmealsRepository;
        $this->importTitleDupeRepository = $importTitleDupeRepository;

        $this->logger = $logger
            ->addFileHandler(ImportForm::class.'.log')
            ->createLogger();
    }
    
    public function ImportForm(string $url): array {
        $regexp = '/-(.*)-(.*)/m';
        preg_match_all($regexp, $url, $matches, PREG_SET_ORDER, 0);

        $planId = $matches[0][1];
        $recipeId = $matches[0][2];

        if (!$this->importDupeRepository->importDupe($planId, $recipeId)) {
            $result = $this->guzzleHTTPClient->request('GET', $url, ['http_errors' => false]);

            $status = $result->getStatusCode();
            $body = $result->getBody();

            if ($status == 200) {
                $recipe = $this->parseEmeals($body);

                if (!$this->importTitleDupeRepository->importTitleDupe($recipe['title'])) {
                    $recipe['id'] = $this->importEmealsRepository->importEmeals($recipe);

                    $archive = [
                        'title' => $recipe['title'],
                        'planId' => $planId,
                        'recipeId' => $recipeId,
                        'body' => $body,
                        'result' => $status
                    ];

                    $this->archiveEmealsRepository->archiveEmeals($archive);
                    
                    $data = [
                        'status'=>'success', 
                        'message'=> $recipe['id']." added successfully",
                        'planId' => $planId,
                        'recipeId' => $recipeId,
                        'title' => $recipe['title']
                    ];
                } else {
                    $data = [
                        'status'=>'fail', 
                        'message'=>'duplicate',
                        'planId' => $planId,
                        'recipeId' => $recipeId
                    ];
                }
            } else {
                $archive = [
                    'title' => null,
                    'planId' => $planId,
                    'recipeId' => $recipeId,
                    'result' => $status
                ];

                $this->archiveEmealsRepository->archiveEmeals($archive);

                $data = [
                    'status'=>'fail', 
                    'message'=>"http error $status",
                    'planId' => $planId,
                    'recipeId' => $recipeId,
                ];
            }
        } else {
            $data = [
                'status'=>'fail', 
                'message'=>'duplicate',
                'planId' => $planId,
                'recipeId' => $recipeId
            ];
        }

        return $data;
    }

    public function compressImage($im,$quality=90): string {
        ob_start(); //Stdout --> buffer
        imagejpeg(imagecreatefromstring($im),NULL,$quality); // output ...
        $imgString = ob_get_contents(); //store stdout in $imgString
        ob_end_clean(); //clear buffer
        @imagedestroy($im); //destroy img
        return $imgString;
    }

    public function parseEmeals(string $body): array {
        libxml_use_internal_errors(true);
        
        $dom = new \DOMDocument();
        $dom->loadHTML($body);

        $recipe = array();

        $xpath = new \DOMXpath($dom);
        if ($xpath->query("//div[contains(@class,'recipe_image')]//img")->item(0) != null) {
            $recipe['image'] = $xpath->query("//div[contains(@class,'recipe_image')]//img")->item(0)->getAttribute('src');

            $recipe['image'] = 'data:image/png;base64,'.base64_encode($this->compressImage(file_get_contents($recipe['image'])));
        }

        $recipe['title'] = $xpath->query("//h3[contains(@class, 'mainTitle')]")->item(0)->nodeValue;
        $recipe['side_title'] = @$xpath->query("//span[contains(@class, 'sideTitle')]")->item(0)->nodeValue;
        $recipe['times'] = $xpath->query("//div[contains(@class, 'times')]")->item(0);
        $recipe['ingredients'] = $xpath->query("//div[contains(@class, 'container') and contains(@class, 'ingredients')]")->item(0);
        $recipe['instructions'] = $xpath->query("//div[contains(@class, 'container') and contains(@class, 'instructions')]")->item(0);
        $recipe['side_ingredients'] = @$xpath->query("//div[contains(@class, 'inner_container') and contains(@class, 'ingredients')]")->item(0);
        $recipe['side_instructions'] = @$xpath->query("//div[contains(@class, 'inner_container') and contains(@class, 'instructions')]")->item(0);


        $items = $recipe['times']->getElementsByTagName('time');
        
        $recipe['times'] = array();
        foreach ($items as $item) {
            if (strpos($item->textContent, 'Prep') !== false) {
                $recipe['times']['prep'] = explode(' ', $item->textContent)[0];
            }
            if (strpos($item->textContent, 'Cook') !== false) {
                $recipe['times']['cook'] = explode(' ', $item->textContent)[0];
            }
            if (strpos($item->textContent, 'Total') !== false) {
                $recipe['times']['total'] = explode(' ', $item->textContent)[0];
            }
        }

        $items = $recipe['ingredients']->getElementsByTagName('li');
        
        $recipe['ingredients'] = array();
        foreach ($items as $item) {
            $recipe['ingredients'][] = $this->parseIngredients($item->textContent);
        }

        $items = $recipe['instructions']->getElementsByTagName('li');
        
        $recipe['instructions'] = array();
        foreach ($items as $item) {
            $recipe['instructions'][] = $item->textContent;
        }

        if ($recipe['side_ingredients'] != false) {
            $items = $recipe['side_ingredients']->getElementsByTagName('li');
            
            $recipe['side_ingredients'] = array();
            foreach ($items as $item) {
                $recipe['side_ingredients'][] = $this->parseIngredients($item->textContent);
            }

            $items = $recipe['side_instructions']->getElementsByTagName('li');
            
            $recipe['side_instructions'] = array();
            foreach ($items as $item) {
                $recipe['side_instructions'][] = $item->textContent;
            }
        }

        return $recipe;
    }

    function parseIngredients(string $ingredient): array {
        $parsedIngredient = array();
        if (preg_match("/[0-9][\/][0-9]|[0-9][[:space:]]?[\x{00BC}-\x{00BE}|\x{2150}-\x{215E}]|[0-9]|[\x{00BC}-\x{00BE}|\x{2150}-\x{215E}]/u", $ingredient)) { //Check To See If The Ingredient contains a certain amount.
            preg_match("/[0-9][\/][0-9]|[0-9][[:space:]]?[\x{00BC}-\x{00BE}|\x{2150}-\x{215E}]|[0-9]|[\x{00BC}-\x{00BE}|\x{2150}-\x{215E}]/u", $ingredient, $matches);
            $parsedIngredient['quantity'] = trim($matches[0]);
            
            //Check To See If We Have a Matching Unit in our $ingredient
            $Units = $this->getCookingUnits();
            
            //Check To See If We Can Find a Matching Unit
            foreach($Units as $Unit){
                if (preg_match("/[[:space:]]".$Unit."[[:space:]]/", $ingredient)) {
                    preg_match("/[[:space:]]".$Unit."[[:space:]]/", $ingredient, $matches);
                    $parsedIngredient['unit'] = trim($matches[0]);
                    break;
                }
            }
        
            //Find The Name of The Item
            //Remove The Unit and Amount 
            $FixedString = $ingredient;
            $FixedString = @str_replace($parsedIngredient['unit'], "", $FixedString);
            $FixedString = @str_replace($parsedIngredient['quantity'], "", $FixedString);  
            
            $ArrayString = explode(",", $FixedString);
            if(count($ArrayString) > 1){
                $parsedIngredient['info'] = trim($ArrayString[1]);
            } 
            $parsedIngredient['name'] = trim($ArrayString[0]);
            

        } else {
            //We Dont a have an ingrident quanity thus we could only have an ingredient and possibly an info value	
            //We Can spilt these two strings up by spilting on the ","
            $ArrayString = explode(",", $ingredient);
            if(count($ArrayString) > 4){
                $parsedIngredient['info'] = $ArrayString[1];
            } 
            $parsedIngredient['name'] = $ArrayString[0];
        }
        
        return $parsedIngredient;
    }

    function getCookingUnits(): array {
        $units = array();
        
        $units[] = "teaspoon"; 
        $units[] = "t"; 
        $units[] = "tsp.";
        $units[] = "tsp"; 
        
        $units[] = "pounds"; 
        $units[] = "lb"; 
        $units[] = "lbs"; 
        $units[] = "lb."; 
        $units[] = "lbs."; 
        
        $units[] = "tablespoon"; 
        $units[] = "T"; 
        $units[] = "tbl."; 
        $units[] = "tbs."; 
        $units[] = "tbsp.";
        $units[] = "Tbsp"; 
        
        $units[] = "cup"; 
        $units[] = "cups"; 
        $units[] = "c"; 
        $units[] = "c."; 
        
        return $units;	
    }
}