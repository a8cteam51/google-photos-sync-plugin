import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import {
	Toolbar,
	ToolbarGroup,
	FormFileUpload,
	PanelBody,
	RangeControl,
} from '@wordpress/components';
import {
	BlockControls,
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { useDispatch } from '@wordpress/data';
import { store as noticesStore } from '@wordpress/notices';

const FileUpload = () => {
	const { createErrorNotice, createInfoNotice } = useDispatch( noticesStore );

	return (
		<FormFileUpload
			accept="image/*"
			onChange={ ( event ) =>
				uploadFiles( event.currentTarget.files, {
					createErrorNotice,
					createInfoNotice,
				} )
			}
			multiple
			className="components-clipboard-toolbar-button"
		>
			{ __( 'Upload', 'google-photos-sync' ) }
		</FormFileUpload>
	);
};

const ToolbarUpload = () => {
	return (
		<BlockControls>
			<ToolbarGroup>
				<Toolbar>
					<FileUpload />
				</Toolbar>
			</ToolbarGroup>
		</BlockControls>
	);
};

/**
 * @param {FileList} files
 * @param {Object}   dispatch
 * @param {Function} dispatch.createErrorNotice
 * @param {Function} dispatch.createInfoNotice
 */
const uploadFiles = ( files, dispatch ) => {
	const { createErrorNotice, createInfoNotice } = dispatch;

	const formData = new FormData();

	for ( const file of files ) {
		formData.append( 'file[]', file );
	}

	apiFetch( {
		path: '/google-photos-sync/v1/upload',
		method: 'POST',
		body: formData,
	} )
		.then( () => {
			createInfoNotice( __( 'Image successfuly uploaded!' ), {
				type: 'snackbar',
				explicitDismiss: true,
			} );
		} )
		.catch( ( error ) => {
			createErrorNotice( __( 'Error uploading image: ' ) + error, {
				type: 'snackbar',
				isDismissible: true,
			} );
		} );
};

export default function Edit( { attributes, setAttributes } ) {
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'google-photos-sync' ) }>
					<RangeControl
						label={ __( 'Columns', 'google-photos-sync' ) }
						value={ attributes.columns }
						onChange={ ( value ) =>
							setAttributes( { columns: value } )
						}
						min={ 1 }
						max={ 6 }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...useBlockProps() }>
				<ToolbarUpload />
				<ServerSideRender
					block="google-photos-sync/album"
					attributes={ attributes }
				/>
			</div>
		</>
	);
}
