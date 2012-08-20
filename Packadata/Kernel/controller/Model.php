<?php

namespace Supersoniq\Packadata\Kernel\Controller;

class Model extends \Controller\Model_Unversioned {


	/*************************************************************************
	 ACTION METHODS
	*************************************************************************/
	public function archives( $type = NULL ) {
		$archives = ( new \Model_Archive( ) )->all( $type );
		return $archives;
	}
	public function archive( $id ) {
		$model = $this->model( );
		$model->init_by_id( $id );
		$archives = ( new \Model_Archive( ) )->get_model_history( $id, $model->type );
		return $archives;
	}
	public function see( $id, $versions ) {
		$model = $this->model( );
		$model->init_by_id( $id );
		$archive = ( new \Model_Archive( ) )->get_model_version( $id, $model->type, array( 'attributes' => $versions ) );
		return $archive;
	}
	public function erase( $id, $versions = NULL ) {
		$model = $this->model( );
		$model->init_by_id( $id );
		if ( isset( $versions ) ) {
			$archives = ( new \Model_Archive( ) )->get_model_version( $id, $model->type, array( 'attributes' => $versions  ) );
		} else {
			$archives = ( new \Model_Archive( ) )->get_model_history( $id, $model->type );
			if ( $archives ) {
				$archives = $archives->to_array( );
			}
		}
		if ( $archives ) {
			if ( is_array( $archives ) ) {
				foreach ( $archives as $archive ) {
					$archive->delete( );
				}
				\Notification::push( 'Archives of this ' . $model->type . ' deleted with success ! ', \Notification::SUCCESS );
				return TRUE;
			} else {
				$deleted_version = $archives->model_type_version . '.' . $archives->model_attributes_version;
				$archives->delete( );
				\Notification::push( 'Version ' . $deleted_version . ' of this ' . $model->type . ' deleted with success ! ', \Notification::SUCCESS );
				return TRUE;
			}
		} else {
			\Notification::push( 'Archives not found !', \Notification::ERROR );
			return FALSE;
		}
	}
	public function restore( $id, $versions ) {
		$force_insert = FALSE;
		$model = $this->model( );
		if ( ! $model->init_by_id( $id ) ) {
			$force_insert = TRUE;
		}
		$archive = ( new \Model_Archive( ) )->get_model_version( $id, $model->type, array( 'attributes' => $versions ) );
		$model = $archive->get_model( );
		$model->attributes_version = $archive->last_version( $id, $model->type )->model_attributes_version;
		$model->save( $force_insert );
	}
}
