﻿/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.dialog.add('link',function(a){
    var b=function(){
        var B=this.getDialog(),C=B.getContentElement('target','popupFeatures'),D=B.getContentElement('target','linkTargetName'),E=this.getValue();
        if(!C||!D)return;
        C=C.getElement();
        C.hide();
        D.setValue('');
        switch(E){
            case 'frame':
                D.setLabel(a.lang.link.targetFrameName);
                D.getElement().show();
                break;
            case 'popup':
                C.show();
                D.setLabel(a.lang.link.targetPopupName);
                D.getElement().show();
                break;
            default:
                D.setValue(E);
                D.getElement().hide();
                break;
        }
    },c=function(){
    var B=this.getDialog(),C=['urlOptions','anchorOptions','emailOptions'],D=this.getValue(),E=B.definition.getContents('upload'),F=E&&E.hidden;
    if(D=='url'){
        if(a.config.linkShowTargetTab)B.showPage('target');
        if(!F)B.showPage('upload');
    }else{
        B.hidePage('target');
        if(!F)B.hidePage('upload');
    }
    for(var G=0;G<C.length;G++){
        var H=B.getContentElement('info',C[G]);
        if(!H)continue;
        H=H.getElement().getParent().getParent();
        if(C[G]==D+'Options')H.show();else H.hide();
    }
    },d=/^javascript:/,e=/^mailto:([^?]+)(?:\?(.+))?$/,f=/subject=([^;?:@&=$,\/]*)/,g=/body=([^;?:@&=$,\/]*)/,h=/^#(.*)$/,i=/^((?:http|https|ftp|news):\/\/)?(.*)$/,j=/^(_(?:self|top|parent|blank))$/,k=/^javascript:void\(location\.href='mailto:'\+String\.fromCharCode\(([^)]+)\)(?:\+'(.*)')?\)$/,l=/^javascript:([^(]+)\(([^)]+)\)$/,m=/\s*window.open\(\s*this\.href\s*,\s*(?:'([^']*)'|null)\s*,\s*'([^']*)'\s*\)\s*;\s*return\s*false;*\s*/,n=/(?:^|,)([^=]+)=(\d+|yes|no)/gi,o=function(B,C){
    var D=C?C.getAttribute('_cke_saved_href')||C.getAttribute('href'):'',E,F,G,H,I={};

    if(E=D.match(d))if(x=='encode')D=D.replace(k,function(Y,Z,aa){
        return 'mailto:'+String.fromCharCode.apply(String,Z.split(','))+(aa&&v(aa));
    });
    else if(x)D.replace(l,function(Y,Z,aa){
        if(Z==y.name){
            I.type='email';
            var ab=I.email={},ac=/[^,\s]+/g,ad=/(^')|('$)/g,ae=aa.match(ac),af=ae.length,ag,ah;
            for(var ai=0;ai<af;ai++){
                ah=decodeURIComponent(v(ae[ai].replace(ad,'')));
                ag=y.params[ai].toLowerCase();
                ab[ag]=ah;
            }
            ab.address=[ab.name,ab.domain].join('@');
        }
    });
if(!I.type)if(G=D.match(h)){
    I.type='anchor';
    I.anchor={};

    I.anchor.name=I.anchor.id=G[1];
}else if(F=D.match(e)){
    var J=D.match(f),K=D.match(g);
    I.type='email';
    var L=I.email={};

    L.address=F[1];
    J&&(L.subject=decodeURIComponent(J[1]));
    K&&(L.body=decodeURIComponent(K[1]));
}else if(D&&(H=D.match(i))){
    I.type='url';
    I.url={};

    I.url.protocol=H[1];
    I.url.url=H[2];
}else I.type='url';
    if(C){
    var M=C.getAttribute('target');
    I.target={};

    I.adv={};

    if(!M){
        var N=C.getAttribute('_cke_pa_onclick')||C.getAttribute('onclick'),O=N&&N.match(m);
        if(O){
            I.target.type='popup';
            I.target.name=O[1];
            var P;
            while(P=n.exec(O[2])){
                if(P[2]=='yes'||P[2]=='1')I.target[P[1]]=true;
                else if(isFinite(P[2]))I.target[P[1]]=P[2];
            }
        }
    }else{
    var Q=M.match(j);
    if(Q)I.target.type=I.target.name=M;
    else{
        I.target.type='frame';
        I.target.name=M;
    }
}
var R=this,S=function(Y,Z){
    var aa=C.getAttribute(Z);
    if(aa!==null)I.adv[Y]=aa||'';
};

S('advId','id');
S('advLangDir','dir');
S('advAccessKey','accessKey');
S('advName','name');
S('advLangCode','lang');
S('advTabIndex','tabindex');
S('advTitle','title');
S('advContentType','type');
S('advCSSClasses','class');
S('advCharset','charset');
S('advStyles','style');
}
var T=B.document.getElementsByTag('img'),U=new CKEDITOR.dom.nodeList(B.document.$.anchors),V=I.anchors=[];
for(var W=0;W<T.count();W++){
    var X=T.getItem(W);
    if(X.getAttribute('_cke_realelement')&&X.getAttribute('_cke_real_element_type')=='anchor')V.push(B.restoreRealElement(X));
}
for(W=0;W<U.count();W++)V.push(U.getItem(W));
for(W=0;W<V.length;W++){
    X=V[W];
    V[W]={
        name:X.getAttribute('name'),
        id:X.getAttribute('id')
        };

}
this._.selectedElement=C;
return I;
},p=function(B,C){
    if(C[B])this.setValue(C[B][this.id]||'');
},q=function(B){
    return p.call(this,'target',B);
},r=function(B){
    return p.call(this,'adv',B);
},s=function(B,C){
    if(!C[B])C[B]={};

    C[B][this.id]=this.getValue()||'';
},t=function(B){
    return s.call(this,'target',B);
},u=function(B){
    return s.call(this,'adv',B);
};

function v(B){
    return B.replace(/\\'/g,"'");
};

function w(B){
    return B.replace(/'/g,'\\$&');
};

var x=a.config.emailProtection||'';
if(x&&x!='encode'){
    var y={};

    x.replace(/^([^(]+)\(([^)]+)\)$/,function(B,C,D){
        y.name=C;
        y.params=[];
        D.replace(/[^,\s]+/g,function(E){
            y.params.push(E);
        });
    });
}
function z(B){
    var C,D=y.name,E=y.params,F,G;
    C=[D,'('];
    for(var H=0;H<E.length;H++){
        F=E[H].toLowerCase();
        G=B[F];
        H>0&&C.push(',');
        C.push("'",G?w(encodeURIComponent(B[F])):'',"'");
    }
    C.push(')');
    return C.join('');
};

function A(B){
    var C,D=B.length,E=[];
    for(var F=0;F<D;F++){
        C=B.charCodeAt(F);
        E.push(C);
    }
    return 'String.fromCharCode('+E.join(',')+')';
};

return{
    title:a.lang.link.title,
    minWidth:350,
    minHeight:230,
    contents:[{
        id:'info',
        label:a.lang.link.info,
        title:a.lang.link.info,
        elements:[{
            id:'linkType',
            type:'select',
            label:a.lang.link.type,
            'default':'url',
            items:[[a.lang.link.toUrl,'url'],[a.lang.link.toAnchor,'anchor'],[a.lang.link.toEmail,'email']],
            onChange:c,
            setup:function(B){
                if(B.type)this.setValue(B.type);
            },
            commit:function(B){
                B.type=this.getValue();
            }
        },{
        type:'vbox',
        id:'urlOptions',
        children:[{
            type:'hbox',
            widths:['25%','75%'],
            children:[{
                id:'protocol',
                type:'select',
                label:a.lang.common.protocol,
                'default':'http://',
                style:'width : 100%;',
                items:[['http://'],['https://'],['ftp://'],['news://'],['<other>','']],
                setup:function(B){
                    if(B.url)this.setValue(B.url.protocol||'');
                },
                commit:function(B){
                    if(!B.url)B.url={};

                    B.url.protocol=this.getValue();
                }
            },{
            type:'text',
            id:'url',
            label:a.lang.common.url,
            required:true,
            onLoad:function(){
                this.allowOnChange=true;
            },
            onKeyUp:function(){
                var G=this;
                G.allowOnChange=false;
                var B=G.getDialog().getContentElement('info','protocol'),C=G.getValue(),D=/^(http|https|ftp|news):\/\/(?=.)/gi,E=/^((javascript:)|[#\/\.])/gi,F=D.exec(C);
                if(F){
                    G.setValue(C.substr(F[0].length));
                    B.setValue(F[0].toLowerCase());
                }else if(E.test(C))B.setValue('');
                G.allowOnChange=true;
            },
            onChange:function(){
                if(this.allowOnChange)this.onKeyUp();
            },
            validate:function(){
                var B=this.getDialog();
                if(B.getContentElement('info','linkType')&&B.getValueOf('info','linkType')!='url')return true;
                if(this.getDialog().fakeObj)return true;
                var C=CKEDITOR.dialog.validate.notEmpty(a.lang.link.noUrl);
                return C.apply(this);
            },
            setup:function(B){
                this.allowOnChange=false;
                if(B.url)this.setValue(B.url.url);
                this.allowOnChange=true;
            },
            commit:function(B){
                this.onChange();
                if(!B.url)B.url={};

                B.url.url=this.getValue();
                this.allowOnChange=false;
            }
        }],
    setup:function(B){
        if(!this.getDialog().getContentElement('info','linkType'))this.getElement().show();
    }
},{
    type:'button',
    id:'browse',
    hidden:'true',
    filebrowser:'info:url',
    label:a.lang.common.browseServer
    }]
},{
    type:'vbox',
    id:'anchorOptions',
    width:260,
    align:'center',
    padding:0,
    children:[{
        type:'fieldset',
        id:'selectAnchorText',
        label:a.lang.link.selectAnchor,
        setup:function(B){
            if(B.anchors.length>0)this.getElement().show();else this.getElement().hide();
        },
        children:[{
            type:'hbox',
            id:'selectAnchor',
            children:[{
                type:'select',
                id:'anchorName',
                'default':'',
                label:a.lang.link.anchorName,
                style:'width: 100%;',
                items:[['']],
                setup:function(B){
                    var E=this;
                    E.clear();
                    E.add('');
                    for(var C=0;C<B.anchors.length;C++){
                        if(B.anchors[C].name)E.add(B.anchors[C].name);
                    }
                    if(B.anchor)E.setValue(B.anchor.name);
                    var D=E.getDialog().getContentElement('info','linkType');
                    if(D&&D.getValue()=='email')E.focus();
                },
                commit:function(B){
                    if(!B.anchor)B.anchor={};

                    B.anchor.name=this.getValue();
                }
            },{
            type:'select',
            id:'anchorId',
            'default':'',
            label:a.lang.link.anchorId,
            style:'width: 100%;',
            items:[['']],
            setup:function(B){
                var D=this;
                D.clear();
                D.add('');
                for(var C=0;C<B.anchors.length;C++){
                    if(B.anchors[C].id)D.add(B.anchors[C].id);
                }
                if(B.anchor)D.setValue(B.anchor.id);
            },
            commit:function(B){
                if(!B.anchor)B.anchor={};

                B.anchor.id=this.getValue();
            }
        }],
    setup:function(B){
        if(B.anchors.length>0)this.getElement().show();else this.getElement().hide();
    }
}]
},{
    type:'html',
    id:'noAnchors',
    style:'text-align: center;',
    html:'<div role="label" tabIndex="-1">'+CKEDITOR.tools.htmlEncode(a.lang.link.noAnchors)+'</div>',
    focus:true,
    setup:function(B){
        if(B.anchors.length<1)this.getElement().show();
        else this.getElement().hide();
    }
}],
setup:function(B){
    if(!this.getDialog().getContentElement('info','linkType'))this.getElement().hide();
}
},{
    type:'vbox',
    id:'emailOptions',
    padding:1,
    children:[{
        type:'text',
        id:'emailAddress',
        label:a.lang.link.emailAddress,
        required:true,
        validate:function(){
            var B=this.getDialog();
            if(!B.getContentElement('info','linkType')||B.getValueOf('info','linkType')!='email')return true;
            var C=CKEDITOR.dialog.validate.notEmpty(a.lang.link.noEmail);
            return C.apply(this);
        },
        setup:function(B){
            if(B.email)this.setValue(B.email.address);
            var C=this.getDialog().getContentElement('info','linkType');
            if(C&&C.getValue()=='email')this.select();
        },
        commit:function(B){
            if(!B.email)B.email={};

            B.email.address=this.getValue();
        }
    },{
    type:'text',
    id:'emailSubject',
    label:a.lang.link.emailSubject,
    setup:function(B){
        if(B.email)this.setValue(B.email.subject);
    },
    commit:function(B){
        if(!B.email)B.email={};

        B.email.subject=this.getValue();
    }
},{
    type:'textarea',
    id:'emailBody',
    label:a.lang.link.emailBody,
    rows:3,
    'default':'',
    setup:function(B){
        if(B.email)this.setValue(B.email.body);
    },
    commit:function(B){
        if(!B.email)B.email={};

        B.email.body=this.getValue();
    }
}],
setup:function(B){
    if(!this.getDialog().getContentElement('info','linkType'))this.getElement().hide();
}
}]
},{
    id:'target',
    label:a.lang.link.target,
    title:a.lang.link.target,
    elements:[{
        type:'hbox',
        widths:['50%','50%'],
        children:[{
            type:'select',
            id:'linkTargetType',
            label:a.lang.common.target,
            'default':'notSet',
            style:'width : 100%;',
            items:[[a.lang.common.notSet,'notSet'],[a.lang.link.targetFrame,'frame'],[a.lang.link.targetPopup,'popup'],[a.lang.common.targetNew,'_blank'],[a.lang.common.targetTop,'_top'],[a.lang.common.targetSelf,'_self'],[a.lang.common.targetParent,'_parent']],
            onChange:b,
            setup:function(B){
                if(B.target)this.setValue(B.target.type);
            },
            commit:function(B){
                if(!B.target)B.target={};

                B.target.type=this.getValue();
            }
        },{
        type:'text',
        id:'linkTargetName',
        label:a.lang.link.targetFrameName,
        'default':'',
        setup:function(B){
            if(B.target)this.setValue(B.target.name);
        },
        commit:function(B){
            if(!B.target)B.target={};

            B.target.name=this.getValue();
        }
    }]
},{
    type:'vbox',
    width:260,
    align:'center',
    padding:2,
    id:'popupFeatures',
    children:[{
        type:'fieldset',
        label:a.lang.link.popupFeatures,
        children:[{
            type:'hbox',
            children:[{
                type:'checkbox',
                id:'resizable',
                label:a.lang.link.popupResizable,
                setup:q,
                commit:t
            },{
                type:'checkbox',
                id:'status',
                label:a.lang.link.popupStatusBar,
                setup:q,
                commit:t
            }]
            },{
            type:'hbox',
            children:[{
                type:'checkbox',
                id:'location',
                label:a.lang.link.popupLocationBar,
                setup:q,
                commit:t
            },{
                type:'checkbox',
                id:'toolbar',
                label:a.lang.link.popupToolbar,
                setup:q,
                commit:t
            }]
            },{
            type:'hbox',
            children:[{
                type:'checkbox',
                id:'menubar',
                label:a.lang.link.popupMenuBar,
                setup:q,
                commit:t
            },{
                type:'checkbox',
                id:'fullscreen',
                label:a.lang.link.popupFullScreen,
                setup:q,
                commit:t
            }]
            },{
            type:'hbox',
            children:[{
                type:'checkbox',
                id:'scrollbars',
                label:a.lang.link.popupScrollBars,
                setup:q,
                commit:t
            },{
                type:'checkbox',
                id:'dependent',
                label:a.lang.link.popupDependent,
                setup:q,
                commit:t
            }]
            },{
            type:'hbox',
            children:[{
                type:'text',
                widths:['30%','70%'],
                labelLayout:'horizontal',
                label:a.lang.link.popupWidth,
                id:'width',
                setup:q,
                commit:t
            },{
                type:'text',
                labelLayout:'horizontal',
                widths:['55%','45%'],
                label:a.lang.link.popupLeft,
                id:'left',
                setup:q,
                commit:t
            }]
            },{
            type:'hbox',
            children:[{
                type:'text',
                labelLayout:'horizontal',
                widths:['30%','70%'],
                label:a.lang.link.popupHeight,
                id:'height',
                setup:q,
                commit:t
            },{
                type:'text',
                labelLayout:'horizontal',
                label:a.lang.link.popupTop,
                widths:['55%','45%'],
                id:'top',
                setup:q,
                commit:t
            }]
            }]
        }]
    }]
},{
    id:'upload',
    label:a.lang.link.upload,
    title:a.lang.link.upload,
    hidden:true,
    filebrowser:'uploadButton',
    elements:[{
        type:'file',
        id:'upload',
        label:a.lang.common.upload,
        style:'height:40px',
        size:29
    },{
        type:'fileButton',
        id:'uploadButton',
        label:a.lang.common.uploadSubmit,
        filebrowser:'info:url',
        'for':['upload','upload']
        }]
    },{
    id:'advanced',
    label:a.lang.link.advanced,
    title:a.lang.link.advanced,
    elements:[{
        type:'vbox',
        padding:1,
        children:[{
            type:'hbox',
            widths:['45%','35%','20%'],
            children:[{
                type:'text',
                id:'advId',
                label:a.lang.link.id,
                setup:r,
                commit:u
            },{
                type:'select',
                id:'advLangDir',
                label:a.lang.link.langDir,
                'default':'',
                style:'width:110px',
                items:[[a.lang.common.notSet,''],[a.lang.link.langDirLTR,'ltr'],[a.lang.link.langDirRTL,'rtl']],
                setup:r,
                commit:u
            },{
                type:'text',
                id:'advAccessKey',
                width:'80px',
                label:a.lang.link.acccessKey,
                maxLength:1,
                setup:r,
                commit:u
            }]
            },{
            type:'hbox',
            widths:['45%','35%','20%'],
            children:[{
                type:'text',
                label:a.lang.link.name,
                id:'advName',
                setup:r,
                commit:u
            },{
                type:'text',
                label:a.lang.link.langCode,
                id:'advLangCode',
                width:'110px',
                'default':'',
                setup:r,
                commit:u
            },{
                type:'text',
                label:a.lang.link.tabIndex,
                id:'advTabIndex',
                width:'80px',
                maxLength:5,
                setup:r,
                commit:u
            }]
            }]
        },{
        type:'vbox',
        padding:1,
        children:[{
            type:'hbox',
            widths:['45%','55%'],
            children:[{
                type:'text',
                label:a.lang.link.advisoryTitle,
                'default':'',
                id:'advTitle',
                setup:r,
                commit:u
            },{
                type:'text',
                label:a.lang.link.advisoryContentType,
                'default':'',
                id:'advContentType',
                setup:r,
                commit:u
            }]
            },{
            type:'hbox',
            widths:['45%','55%'],
            children:[{
                type:'text',
                label:a.lang.link.cssClasses,
                'default':'',
                id:'advCSSClasses',
                setup:r,
                commit:u
            },{
                type:'text',
                label:a.lang.link.charset,
                'default':'',
                id:'advCharset',
                setup:r,
                commit:u
            }]
            },{
            type:'hbox',
            children:[{
                type:'text',
                label:a.lang.link.styles,
                'default':'',
                id:'advStyles',
                setup:r,
                commit:u
            }]
            }]
        }]
    }],
onShow:function(){
    var H=this;
    H.fakeObj=false;
    var B=H.getParentEditor(),C=B.getSelection(),D=C.getRanges(),E=null,F=H;
    if(D.length==1){
        var G=D[0].getCommonAncestor(true);
        E=G.getAscendant('a',true);
        if(E&&E.getAttribute('href'))C.selectElement(E);
        else if((E=G.getAscendant('img',true))&&E.getAttribute('_cke_real_element_type')&&E.getAttribute('_cke_real_element_type')=='anchor'){
            H.fakeObj=E;
            E=B.restoreRealElement(H.fakeObj);
            C.selectElement(H.fakeObj);
        }else E=null;
    }
    H.setupContent(o.apply(H,[B,E]));
},
onOk:function(){
    var B={
        href:'javascript:void(0)/*'+CKEDITOR.tools.getNextNumber()+'*/'
        },C=[],D={
        href:B.href
        },E=this,F=this.getParentEditor();
    this.commitContent(D);
    switch(D.type||'url'){
        case 'url':
            var G=D.url&&D.url.protocol!=undefined?D.url.protocol:'http://',H=D.url&&D.url.url||'';
            B._cke_saved_href=H.indexOf('/')===0?H:G+H;
            break;
        case 'anchor':
            var I=D.anchor&&D.anchor.name,J=D.anchor&&D.anchor.id;
            B._cke_saved_href='#'+(I||J||'');
            break;
        case 'email':
            var K,L=D.email,M=L.address;
            switch(x){
            case '':case 'encode':
                var N=encodeURIComponent(L.subject||''),O=encodeURIComponent(L.body||''),P=[];
                N&&P.push('subject='+N);
                O&&P.push('body='+O);
                P=P.length?'?'+P.join('&'):'';
                if(x=='encode'){
                K=["javascript:void(location.href='mailto:'+",A(M)];
                P&&K.push("+'",w(P),"'");
                K.push(')');
            }else K=['mailto:',M,P];
                break;
            default:
                var Q=M.split('@',2);
                L.name=Q[0];
                L.domain=Q[1];
                K=['javascript:',z(L)];
        }
        B._cke_saved_href=K.join('');
            break;
    }
    if(D.target)if(D.target.type=='popup'){
        var R=["window.open(this.href, '",D.target.name||'',"', '"],S=['resizable','status','location','toolbar','menubar','fullscreen','scrollbars','dependent'],T=S.length,U=function(ag){
            if(D.target[ag])S.push(ag+'='+D.target[ag]);
        };

        for(var V=0;V<T;V++)S[V]=S[V]+(D.target[S[V]]?'=yes':'=no');
        U('width');
        U('left');
        U('height');
        U('top');
        R.push(S.join(','),"'); return false;");
        B._cke_pa_onclick=R.join('');
    }else{
        if(D.target.type!='notSet'&&D.target.name)B.target=D.target.name;else C.push('target');
        C.push('_cke_pa_onclick','onclick');
    }
    if(D.adv){
        var W=function(ag,ah){
            var ai=D.adv[ag];
            if(ai)B[ah]=ai;else C.push(ah);
        };

        if(this._.selectedElement)W('advId','id');
        W('advLangDir','dir');
        W('advAccessKey','accessKey');
        W('advName','name');
        W('advLangCode','lang');
        W('advTabIndex','tabindex');
        W('advTitle','title');
        W('advContentType','type');
        W('advCSSClasses','class');
        W('advCharset','charset');
        W('advStyles','style');
    }
    if(!this._.selectedElement){
        var X=F.getSelection(),Y=X.getRanges();
        if(Y.length==1&&Y[0].collapsed){
            var Z=new CKEDITOR.dom.text(B._cke_saved_href,F.document);
            Y[0].insertNode(Z);
            Y[0].selectNodeContents(Z);
            X.selectRanges(Y);
        }
        var aa=new CKEDITOR.style({
            element:'a',
            attributes:B
        });
        aa.type=CKEDITOR.STYLE_INLINE;
        aa.apply(F.document);
        if(D.adv&&D.adv.advId){
            var ab=this.getParentEditor().document.$.getElementsByTagName('a');
            for(V=0;V<ab.length;V++){
                if(ab[V].href==B.href){
                    ab[V].id=D.adv.advId;
                    break;
                }
            }
            }
    }else{
    var ac=this._.selectedElement,ad=ac.getAttribute('_cke_saved_href'),ae=ac.getHtml();
    if(CKEDITOR.env.ie&&B.name!=ac.getAttribute('name')){
        var af=new CKEDITOR.dom.element('<a name="'+CKEDITOR.tools.htmlEncode(B.name)+'">',F.document);
        X=F.getSelection();
        ac.moveChildren(af);
        ac.copyAttributes(af,{
            name:1
        });
        af.replace(ac);
        ac=af;
        X.selectElement(ac);
    }
    ac.setAttributes(B);
    ac.removeAttributes(C);
    if(ad==ae)ac.setHtml(B._cke_saved_href);
    if(ac.getAttribute('name'))ac.addClass('cke_anchor');else ac.removeClass('cke_anchor');
    if(this.fakeObj)F.createFakeElement(ac,'cke_anchor','anchor').replace(this.fakeObj);
    delete this._.selectedElement;
}
},
onLoad:function(){
    if(!a.config.linkShowAdvancedTab)this.hidePage('advanced');
    if(!a.config.linkShowTargetTab)this.hidePage('target');
},
onFocus:function(){
    var B=this.getContentElement('info','linkType'),C;
    if(B&&B.getValue()=='url'){
        C=this.getContentElement('info','url');
        C.select();
    }
}
};

});
