<?php

namespace Classes;

class FnArray
{
    /**
     * Compara dos arreglos ($array1 y $array2) a través de una clave común ($key). Busca los elementos que aparecen en ambos arreglos y devuelve un arreglo que contenga tres arreglos distintos: uno con los elementos duplicados provenientes del $array1, otro con los elementos duplicados provenientes del $array2, y un tercer arreglo con los elementos no duplicados..
     *
     * @param array $array1 El primer arreglo a comparar.
     * @param array $array2 El segundo arreglo a comparar.
     * @param string $key La clave en la cual se basará la comparación.
     * @return array Un arreglo con tres arreglos distintos: duplicados1, duplicados2 y no_duplicados.
     */
    public function comparar($array1, $array2, $key)
    {
        $no_duplicados = array();
        $duplicados1 = array();
        $duplicados2 = array();
        // Iterar sobre el primer array y crear un nuevo array indexado por "$key"
        foreach ($array1 as $item) {
            $no_duplicados[$item[$key]] = $item;
        }
        // Iterar sobre el segundo array y verificar si la clave "$key" existe en el nuevo array
        // Si la clave ya existe, agregar el sub-array correspondiente a un nuevo array que contendrá los duplicados.
        foreach ($array2 as $item) {
            if (isset($no_duplicados[$item[$key]])) {
                $duplicados1[] = $no_duplicados[$item[$key]];
                $duplicados2[] = $item;
                unset($no_duplicados[$item[$key]]); // Eliminar el elemento duplicado del array de elementos no duplicados
            }
        }
        // Retornar el array de elementos duplicados y no duplicados
        return array(
            'duplicados1' => $duplicados1,
            // Datos duplicaodos con los valores recicibidos en el array1
            'duplicados2' => $duplicados2,
            // Datos duplicaodos con los valores recicibidos del array2
            'no_duplicados' => $no_duplicados, // Datos no duplicados
        );
    }

    /** 
     * Toma como parámetro un array multidimensional y elimina subarrays vacíos. Se recorre cada subarray dentro del array principal y se comprueba si está vacío o no. Si el subarray está vacío, se elimina ese subarray del array principal. la función retorna el array resultante con los subarrays vacíos eliminados.
     * @param array arrayMultidimensional
     */
    public static function removeEmptySubarrays($array)
    {
        if ($array) {
            foreach ($array as $key => $subarray) {
                if (empty($subarray)) {
                    unset($array[$key]);
                }
            }
            return array_values($array);
        }
        return '';
    }
}
