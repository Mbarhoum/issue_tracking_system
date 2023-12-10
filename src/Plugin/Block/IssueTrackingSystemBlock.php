<?php

namespace Drupal\issue_tracking_system\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;


/**
 * Provides a 'Issue Tracking System Block' block.
 *
 * @Block(
 *   id = "issue_tracking_system_block",
 *   admin_label = @Translation("Issue Tracking System Block"),
 * )
 */
class IssueTrackingSystemBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */


  public function build() {
    $current_user = \Drupal::currentUser();
    if ($current_user->isAuthenticated()) {
     $uid = $current_user->id();
   }
   $query = \Drupal::entityQuery('node')
   ->condition('type', 'issue_tracking_system')
   ->condition('status', 1)
   ->condition('field_assignee', $uid)
   ->range(0, 3);
   $query->accessCheck(TRUE);

   $results= $query->execute();

   $nodes= Node::loadMultiple($results);

   $header = [
     '#',
     'Title',
     'Status', 
     'Due Date',
   ];

   foreach($nodes as $key => $node){
    $term = Term::load($node->field_status->target_id);
    $status = $term->getName();
    $node_data[] = [
      '#' => $key,
      "title" => $node->title->value,
      'status' => $status,
      'Date' => $node->field_due_date->value
    ];
  }

  foreach ($node_data as $row) {
    $rows[] = $row;
  }

  // table render element.
  $table = [
    '#theme' => 'table',
    '#header' => $header,
    '#rows' => $rows,
    '#empty' => 'No data available',
  ];

  return [
    $table,
    '#attached' => [
      'library' => [
        'issue_tracking_system/tracking_system',
      ]
    ],
  ];
 }
}