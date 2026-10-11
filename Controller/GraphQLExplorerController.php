<?php
/**
 * Date: 31.08.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQLBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class GraphQLExplorerController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/graphql/explorer', name: 'youshido_graphql_graphqlexplorer_explorer')]
    public function explorerAction()
    {
        $response = $this->render('@GraphQLBundle/Feature/explorer.html.twig', [
            'graphQLUrl' => $this->generateUrl('youshido_graphql_graphql_default'),
            'tokenHeader' => 'access-token'
        ]);

        $date = \DateTime::createFromFormat('U', strtotime('tomorrow'), new \DateTimeZone('UTC'));
        $response->setExpires($date);
        $response->setPublic();

        return $response;
    }
}
