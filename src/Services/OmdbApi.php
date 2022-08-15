<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OmdbApi
{
    public static $defaultUrlPoster = 'https://i.picsum.photos/id/102/4320/3240.jpg?hmac=ico2KysoswVG8E8r550V_afIWN963F6ygTVrqHeHeRc';
    private $client;
    private $apiKey = 'tagada';

    public function __construct(HttpClientInterface $client, ContainerBagInterface $params)
    {
        $this->client = $client;
        // je peux maintenant lire TOUS les paramètres du fichiers services.yaml
        $this->apiKey = $params->get('app.omdbapi.key');
    }

    public function fetchOmdbData(string $titre): array
    {
        // ici on lance la requete HTTP, et on reçoit la réponse
        $response = $this->client->request(
            'GET',
            'https://www.omdbapi.com/?t=' . $titre . '&apikey=' . $this->apiKey
        );

        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'

        //! PHP ne sais pas native lire du JSON
        // solution la plus basique pour lire du json : le transformer en tableau
        $content = $response->toArray();
        
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        return $content;
    }

    public function fetchPoster($titre)
    {
        $content = $this->fetchOmdbData($titre);
        // TODO : Gérer le fait d'avoir une erreur : 
        /**
         * {
            "Response": "False",
            "Error": "Movie not found!"
            }
        */ 
        // si je trouve la clé poster, je renvoit la valeur, sinon je renvoit l'image par défaut
        /// la notation avec ?? signifie je teste la nullité, si oui, je renvoit la valeur donné après
        if (array_key_exists("Poster", $content))
        {
            return $content['Poster'];
        }
        
        return  OmdbApi::$defaultUrlPoster;
        
        $var = "dead code";
        
        //return $content['Poster'] ?? OmdbApi::$defaultUrlPoster;
    }
}