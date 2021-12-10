<?php

namespace App\Domain\Service\Investments\Symbols;

use App\Domain\Repository\Investments\Symbols\CreateRepository;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;
use Michelf\Markdown;

final class Create {
    private $createRepository;

    public function __construct(CreateRepository $createRepository) {
        $this->createRepository = $createRepository;
    }
    
    public function create(array $args): array {
        $validator = v::key('symbol', v::stringType()->length(1, 8))
            ->key('type', v::stringType()->oneOf(v::equals('Crypto'), v::equals('Stock')))
            ->key('name', v::stringType()->length(1, 64));
            
        try {
            $validator->assert($args);
        } catch(NestedValidationException $e) {
            return ['status'=>'fail', 'message'=> Markdown::defaultTransform($e->getFullMessage())];
        }
        
        $id = $this->createRepository->create($args);
        
        $data = [
            'status'=>'success', 
            'id'=>$id
        ];

        return $data;
    }
}