<?php


namespace App\ShopifyApi\Graphql;


use Socialhead\Core\Shopify\GraphSDK;

class ProductQl extends GraphSDK
{
    function getProducts($relNext = null, $revPrev = null, $keyword = null, $limit = 10)
    {
        $query = "query: \"published_status:published";
        $condition = "first: $limit";
        if( ! empty($relNext))
            $condition = "first: $limit, after: \"$relNext\"";

        if( ! empty($revPrev))
            $condition = "last: $limit, before: \"$revPrev\"";

        if( ! empty($keyword))
            $query .= ",title:*$keyword*";

        $query .= "\"";
        $queryString = "{
            products(sortKey: TITLE, $query, $condition) {
            edges {
              node {
                id
                title
                handle
                onlineStorePreviewUrl
                featuredImage{
                  originalSrc
                }
                priceRangeV2{
                  minVariantPrice{
                    amount
                    currencyCode
                  }
                  maxVariantPrice{
                    amount
                    currencyCode
                  }

                }
              }
              cursor
            },
            pageInfo {
              hasNextPage
              hasPreviousPage
            }
          }
        }";
        $query = json_encode([
            'query' => $queryString
        ]);

        $result = $this->graphqlQuery($query);

        return $result;
    }


    /**
     * @param $id
     * @return array
     */
    function getProduct($id)
    {
        $queryString = "{
                          product(id: \"gid://shopify/Product/$id\") {
                            id
                            variants(first: 100) {
                              edges{
                                node{
                                  id
                                  price
                                  compareAtPrice
                                }
                              }
                            }
                          }
                        }";

        $query = json_encode([
            'query' => $queryString
        ]);
        $result = $this->graphqlQuery($query);
        return $result;
    }

    function productVariantsBulkUpdate($variables, $productId)
    {
        $graphQL = <<<'JSON'
                mutation productVariantsBulkUpdate($variants: [ProductVariantsBulkInput!]!, $productId: ID!) {
                  productVariantsBulkUpdate(variants: $variants, productId: $productId) {
                    product {
                      id
                    }
                    productVariants {
                      id
                    }
                    userErrors {
                      code
                      field
                      message
                    }
                  }
                }
                JSON;

        $variables = [
            'variants' => $variables,
            "productId" => $productId
        ];
        $query = json_encode([
            'query' => $graphQL,
            'variables' => $variables
        ]);
        $result = $this->graphqlQuery($query);
        return $result;
    }
}
