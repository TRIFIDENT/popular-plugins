<?php

namespace ProfilePress\Core\Admin\SettingsPages\Membership\DashboardPage;

use ProfilePress\Core\Membership\Models\Order\OrderStatus;
use ProfilePress\Core\Membership\Models\Order\OrderType;

class Orders extends AbstractReport
{
    public function get_total($start_date = '', $end_date = '')
    {
        static $bucket = [];

        $cache_key = md5('orders_get_total' . $start_date . $end_date);

        if ( ! isset($bucket[$cache_key])) {
            $start_date = ! empty($start_date) ? $start_date : '';
            $end_date   = ! empty($end_date) ? $end_date : '';

            $where_clause = $this->get_where_clause(false, $start_date, $end_date);

            $bucket[$cache_key] = $this->wpdb()->get_var(
                $this->wpdb()->prepare(
                    "SELECT COUNT(id) FROM {$this->db_table} WHERE status = %s AND $where_clause", OrderStatus::COMPLETED
                )
            );
        }

        return ! $bucket[$cache_key] ? 0 : $bucket[$cache_key];
    }

    public function get_data()
    {
        $new_orders = $this->wpdb()->get_results(
            $this->wpdb()->prepare(
                "SELECT COUNT(id) AS order_total, {$this->get_date_column()} FROM {$this->db_table} WHERE status = %s AND order_type = %s AND {$this->get_where_clause($groupBy=true)}",
                OrderStatus::COMPLETED, OrderType::NEW_ORDER
            ),
            'ARRAY_A'
        );

        $renewal_orders = $this->wpdb()->get_results(
            $this->wpdb()->prepare(
                "SELECT COUNT(id) AS order_total, {$this->get_date_column()} FROM {$this->db_table} WHERE status = %s AND order_type = %s AND {$this->get_where_clause($groupBy=true)}",
                OrderStatus::COMPLETED, OrderType::RENEWAL_ORDER
            ),
            'ARRAY_A'
        );

        $upgrade_orders = $this->wpdb()->get_results(
            $this->wpdb()->prepare(
                "SELECT COUNT(id) AS order_total, {$this->get_date_column()} FROM {$this->db_table} WHERE status = %s AND order_type = %s AND {$this->get_where_clause($groupBy=true)}",
                OrderStatus::COMPLETED, OrderType::UPGRADE
            ),
            'ARRAY_A'
        );

        $downgrade_orders = $this->wpdb()->get_results(
            $this->wpdb()->prepare(
                "SELECT COUNT(id) AS order_total, {$this->get_date_column()} FROM {$this->db_table} WHERE status = %s AND order_type = %s AND {$this->get_where_clause($groupBy=true)}",
                OrderStatus::COMPLETED, OrderType::DOWNGRADE
            ),
            'ARRAY_A'
        );

        $no_dataset = [];
        $ro_dataset = [];
        $uo_dataset = [];
        $do_dataset = [];

        foreach ($this->get_labels() as $datetime => $label) {

            $no_data = $this->get_interval_data($new_orders, $datetime);
            $ro_data = $this->get_interval_data($renewal_orders, $datetime);
            $uo_data = $this->get_interval_data($upgrade_orders, $datetime);
            $do_data = $this->get_interval_data($downgrade_orders, $datetime);

            $no_dataset[] = ! empty($no_data) ? reset($no_data)['order_total'] : 0;
            $ro_dataset[] = ! empty($ro_data) ? reset($ro_data)['order_total'] : 0;
            $uo_dataset[] = ! empty($uo_data) ? reset($uo_data)['order_total'] : 0;
            $do_dataset[] = ! empty($do_data) ? reset($do_data)['order_total'] : 0;
        }

        return [
            [
                'label'           => esc_html__('New', 'wp-user-avatar'),
                'data'            => $no_dataset,
                'borderColor'     => 'rgb(54, 162, 235)',
                'backgroundColor' => 'rgba(54, 162, 235, 0.5)'
            ],
            [
                'label'           => esc_html__('Renewals', 'wp-user-avatar'),
                'data'            => $ro_dataset,
                'borderColor'     => 'rgb(255, 99, 132)',
                'backgroundColor' => 'rgba(255, 99, 132, 0.5)'
            ],
            [
                'label'           => esc_html__('Upgrades', 'wp-user-avatar'),
                'data'            => $uo_dataset,
                'borderColor'     => 'rgb(75,192,192)',
                'backgroundColor' => 'rgba(75,192,192, 0.5)'
            ],
            [
                'label'           => esc_html__('Downgrades', 'wp-user-avatar'),
                'data'            => $do_dataset,
                'borderColor'     => 'rgb(51,53,56)',
                'backgroundColor' => 'rgba(51,53,56, 0.5)'
            ]
        ];
    }
}