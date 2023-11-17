import { styled } from '@mui/material/styles';
import Button from '@mui/material/Button';

export const BootstrapButton = styled( Button )( {
	boxShadow: 'none',
	textTransform: 'none',
	color: 'white',
	padding: '6px 12px',
	border: '1px solid',
	lineHeight: 1.5,
	backgroundColor: 'var(--color-primary)',
	borderColor: 'var(--color-primary)',
	fontFamily: 'var(--font-body)',
	fontSize: '1.25rem',
	transition: 'all 0.2s ease-in-out',
	'&:hover': {
		backgroundColor: 'white',
		borderColor: 'var(--color-primary)',
		color: 'var(--color-primary)',
		boxShadow: 'none',
	},
	'&:active': {
		boxShadow: 'none',
		backgroundColor: 'var(--color-primary)',
		borderColor: 'var(--color-primary)',
	},
	'&:focus': {
		boxShadow: '0 0 0 0.2rem rgba(0,123,255,.5)',
	},
} );
