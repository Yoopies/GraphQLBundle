<?php

/**
 * Date: 23.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQLBundle\Field;

use Youshido\GraphQL\Field\AbstractField as BaseAbstractField;
use Youshido\GraphQLBundle\DependencyInjection\ContainerAwareInterface;
use Youshido\GraphQLBundle\DependencyInjection\ContainerAwareTrait;


abstract class AbstractContainerAwareField extends BaseAbstractField implements ContainerAwareInterface
{

    use ContainerAwareTrait;

}
