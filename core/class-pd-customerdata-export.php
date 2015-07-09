<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class PD_CustomerData_Export
 *
 * Exports the CSV.
 *
 * @since   0.1.0
 */
class PD_CustomerData_Export {

	private $output;
	private $columns;
	private $filename;
	private $data;

	function __construct( $columns, $data, $filename = false ) {

		$this->columns = $columns;
		$this->data = $data;
		$this->filename = $filename ? $filename : 'pd-list-' . date( 'm-d-Y' );

		$this->headers();
		$this->begin_output();
		$this->output_rows();
		$this->close_output();
	}

	private function headers() {
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=' . $this->filename . '.csv');
	}

	private function begin_output() {
		$this->output = fopen('php://output', 'w');
	}

	private function output_rows() {

		$this->column_headers();

		// loop over the rows, outputting them
		foreach ( $this->data as $row ) {
			fputcsv($this->output, $row);
		}
	}

	private function column_headers() {
		fputcsv($this->output, $this->columns);
	}

	private function close_output() {
		exit();
	}
}