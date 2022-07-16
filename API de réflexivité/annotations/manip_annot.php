<?php
require 'addendum/annotations.php';
require 'MyAnnotations.php';
require 'Personnage.php';

$reflectedClass = new ReflectionAnnotatedClass('Personnage');

$classInfo = $reflectedClass->getAnnotation('ClassInfo');
echo 'classe ' , $classInfo->name , ' qui fait : ' , $classInfo->description , '<br />';
var_dump($classInfo);

$attrInfo = new ReflectionAnnotatedProperty('Personnage', '_force');
echo 'Valeur de l\'attribut' , $attrInfo->getAllAnnotations('AttrInfo')->value , '<br />';
var_dump($attrInfo->getAllAnnotations('AttrInfo'));

$methodInfo = new ReflectionAnnotatedMethod('Personnage', 'deplacer');
foreach($methodInfo->getAnnotation('MethodInfo') as $key => $value)
	echo 'Nom ' , $key , ' => valeur ' , $value , '<br />';
	