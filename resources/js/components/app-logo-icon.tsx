import type { SVGAttributes } from 'react';

export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
       <img src="/agrogreen-logo.png" className="fill-current h-18 w-18" {...props} />
    );
}
