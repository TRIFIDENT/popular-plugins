<?php

/**
 * Add notification when returning visitor rate is lower than 10%
 * Recurrence: 15 Days
 *
 * @since 7.12.3
 */
final class ExactMetrics_Notification_Returning_Visitors extends ExactMetrics_Notification_Event {

	public $notification_id       = 'exactmetrics_notification_returning_visitors';
	public $notification_interval = 30; // in days
	public $notification_type     = array( 'basic', 'lite', 'master', 'plus', 'pro' );
	public $notification_category = 'insight';
	public $notification_priority = 2;

	/**
	 * Build Notification
	 *
	 * @return array $notification notification is ready to add
	 *
	 * @since 7.12.3
	 */
	public function prepare_notification_data( $notification ) {
		$report    = $this->get_report( 'overview', $this->report_start_from, $this->report_end_to );
		$returning = isset( $report['data']['newvsreturn']['returning'] ) ? $report['data']['newvsreturn']['returning'] : 0;

		if ( $returning < 25 ) {

			$is_em = function_exists( 'ExactMetrics' );

			$learn_more_url = 'https://www.exactmetrics.com/optinmonster-review/';

			// Translators: Returning visitors notification title
			$notification['title'] = sprintf( __( 'Only %s%% of Your Visitors Return to Your Site', 'google-analytics-dashboard-for-wp' ), $returning );
			// Translators: Returning visitors notification content
			$notification['content'] = sprintf( __( 'Your site has received a low number of returning users over the past 30 days. Try a tool like %1$sOptinMonster%2$s to increase engagement.', 'google-analytics-dashboard-for-wp' ), '<a href="' . $this->build_external_link( 'https://www.exactmetrics.com/optinmonster-review/' ) . '" target="_blank">', '</a>' );

			if ( ! $is_em ) {
				$notification['btns'] = array(
					'learn_more' => array(
						'url'         => $this->build_external_link( $learn_more_url ),
						'text'        => __( 'Learn More', 'google-analytics-dashboard-for-wp' ),
						'is_external' => true,
					),
				);
			}

			return $notification;
		}

		return false;
	}

}

// initialize the class
new ExactMetrics_Notification_Returning_Visitors();