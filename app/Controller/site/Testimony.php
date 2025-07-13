<?php

namespace App\Controller\site;

use \App\Core\View;
use \App\Model\Entity\Testimony as Entity;
use \WilliamCosta\DatabaseManager\Pagination;
class Testimony extends Page{
    /**
     * Método responsável por obter a renderização dos itens de depoimentos para a página
     * @return array
     */
    private static function getTestimonyItems($request, &$obPagination){
        $quantidadeTotal = Entity::getTestimonies(null, null, null, 'COUNT(*) qtd')->fetchObject()->qtd;
        

        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 2);
        $results = Entity::getTestimonies(null,'id DESC', $obPagination->getLimit());
        
       // Renderiza o item.
        // O método fetchObject(Entity::class) percorre cada linha retornada do banco,
        // criando um novo objeto da classe Entity com os dados da linha atual.
        // Cada propriedade pública da classe Entity é preenchida com os valores correspondentes do banco.
        // O loop while continua até que todas as linhas sejam processadas.
        $itens = [];
        while($obTestimony = $results->fetchObject(Entity::class)) {
            $itens[] = [ 
           'nome' => $obTestimony->nome,
           'mensagem' => $obTestimony->mensagem,
           'data' => date("d/m/Y H:i:s", strtotime($obTestimony->data))
        ];
        }

        return $itens;
    }

    // private static function getTestimonyItems(){
    //     $itens = '';
        
    //     $results = Entity::getTestimonies(null,'id DESC');
        
    //    // Renderiza o item.
    ////Nao é recomendável utilizar fetchObjetc por limitar (a classe tem que estar igual ao BD)
    //    $testimoniesArray = $results->fetchAll(\PDO::FETCH_ASSOC);


    //     foreach ($testimoniesArray as $data) {
    //         $itens[] = Entity::fromArray($data);
    //     }

    //     return $itens;
    // }

    /**
     * Método responsável por retornar o conteúdo (view)
     * @return string
     */
    public static function getTestimonies($request){

        return View::render("site/pages/testimonies/index.twig",[ 
            'title' => 'Depoimentos',
            'itens' => self::getTestimonyItems($request, $obPagination),
            'pagination' => parent::getPagination($request,$obPagination),
        ]);
    }

    public static function insertTestimony($request){
       
        $postVars = $request->getPostVars();
         
        $obTestimony = new Entity;
        $obTestimony->nome = $postVars['nome'];
        $obTestimony->mensagem = $postVars['mensagem'];

        $obTestimony->cadastrar();
        return self::getTestimonies($request);
    }
}