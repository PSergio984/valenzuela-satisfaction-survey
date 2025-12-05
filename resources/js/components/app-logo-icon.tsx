import { SVGAttributes } from 'react';

export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <svg {...props} viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path
                fillRule="evenodd"
                clipRule="evenodd"
                d="M7 2C5.34315 2 4 3.34315 4 5V19C4 20.6569 5.34315 22 7 22H17C18.6569 22 20 20.6569 20 19V5C20 3.34315 18.6569 2 17 2H7ZM8 6C7.44772 6 7 6.44772 7 7C7 7.55228 7.44772 8 8 8H16C16.5523 8 17 7.55228 17 7C17 6.44772 16.5523 6 16 6H8ZM7 11C7 10.4477 7.44772 10 8 10H16C16.5523 10 17 10.4477 17 11C17 11.5523 16.5523 12 16 12H8C7.44772 12 7 11.5523 7 11ZM8 14C7.44772 14 7 14.4477 7 15C7 15.5523 7.44772 16 8 16H12C12.5523 16 13 15.5523 13 15C13 14.4477 12.5523 14 12 14H8Z"
            />
        </svg>
    );
}
