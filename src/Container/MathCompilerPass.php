<?php

/**
 * @file
 * Contains \Drupal\currency\Container\MathCompilerPass.
 */

namespace Drupal\currency\Container;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers math handlers.
 */
class MathCompilerPass implements CompilerPassInterface {

  /**
   * {@inheritdoc}
   */
  public function process(ContainerBuilder $container) {
    $tag_name = 'currency.math';
    $tags = $container->findTaggedServiceIds($tag_name);

    // Validate the tags.
    foreach ($tags as $service_id => $handler_tags) {
      if (count($handler_tags) > 1) {
        throw new \Exception(sprintf('Service %s cannot be tagged with %s more than once.', $service_id, $tag_name));
      }
      if (!isset($handler_tags[0]['weight'])) {
        throw new \Exception(sprintf('Service %s was tagged with %s, but the tag does not contain a "weight" item.',
          $service_id, $tag_name));
      }
      if (!is_numeric($handler_tags[0]['weight'])) {
        throw new \Exception(sprintf('Service %s was tagged with %s, but its "weight" item has a non-numeric value.',
          $service_id, $tag_name));
      }
    }

    // Sort handlers by weight.
    uasort($tags, function(array $handler_tags_a, array $handler_tags_b) {
      return $handler_tags_a[0]['weight'] == $handler_tags_b[0]['weight'] ? 0 : $handler_tags_a[0]['weight'] > $handler_tags_b[0]['weight'] ? 1 : -1;
    });

    // Register the handlers with the math delegator.
    $delegator_definition = $container->getDefinition('currency.math.environment_compatible_math_delegator');
    foreach (array_keys($tags) as $service_id) {
      $delegator_definition->addMethodCall('addMathHandler', [new Reference($service_id)]);
    }
  }

}
